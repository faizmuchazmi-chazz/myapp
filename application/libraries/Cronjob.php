<?php
class Cronjob
{
	/**
	 * Sync all cron jobs defined in config/cronjobs.php.
	 * Adds missing ones, updates changed schedules, leaves unrelated ones alone.
	 *
	 * @return array  [ 'ok' => [...labels], 'failed' => [...labels] ]
	 */
	public function sync()
	{
		$CI = &get_instance();
		$CI->config->load('cronjobs');
		$jobs = $CI->config->item('cronjobs');

		$result = ['ok' => [], 'failed' => []];

		foreach ($jobs as $job) {
			$success = $this->ensure_one_only($job['expression'], $job['command']);
			$result[$success ? 'ok' : 'failed'][] = $job['label'];
		}

		return $result;
	}

	/**
	 * Ensure only one instance of a command exists in crontab.
	 * Updates the schedule if the command exists with a different expression.
	 *
	 * @param  string $cronExpression  e.g. "5 * * * *"
	 * @param  string $command         e.g. "/usr/bin/php /var/www/html/index.php cli run_task"
	 * @return bool
	 */
	public function ensure_one_only($cronExpression, $command)
	{
		$desiredLine = $this->_build_line($cronExpression, $command);
		$currentCron = $this->_read_crontab();

		$found    = FALSE;
		$newCron  = [];

		foreach ($currentCron as $line) {
			$isMatch = strpos($line, $command) !== FALSE;

			if ($isMatch && ! $found) {
				if (trim($line) === $desiredLine) {
					return TRUE; // Exact match, nothing to do
				}
				$newCron[] = $desiredLine; // Replace with updated schedule
				$found     = TRUE;
			} elseif ($isMatch && $found) {
				// Skip duplicate entries of the same command
				continue;
			} else {
				$newCron[] = $line;
			}
		}

		if (! $found) {
			$newCron[] = $desiredLine;
		}

		return $this->_write_crontab($newCron);
	}

	/**
	 * Add a cron job only if the exact line doesn't already exist.
	 *
	 * @param  string $cronExpression
	 * @param  string $command
	 * @return bool
	 */
	public function add($cronExpression, $command)
	{
		$desiredLine = $this->_build_line($cronExpression, $command);
		$currentCron = $this->_read_crontab();

		foreach ($currentCron as $line) {
			if (trim($line) === $desiredLine) {
				return TRUE; // Already exists
			}
		}

		$currentCron[] = $desiredLine;
		return $this->_write_crontab($currentCron);
	}

	/**
	 * Remove all cron jobs matching the given command string.
	 *
	 * @param  string $command
	 * @return bool
	 */
	public function remove($command)
	{
		$currentCron = $this->_read_crontab();

		$newCron = array_values(array_filter($currentCron, function ($line) use ($command) {
			return strpos($line, $command) === FALSE;
		}));

		if (count($newCron) === count($currentCron)) {
			return TRUE; // Nothing to remove
		}

		return $this->_write_crontab($newCron);
	}

	/**
	 * Check if a cron job exists by command (any schedule).
	 *
	 * @param  string $command
	 * @return bool
	 */
	public function exists($command)
	{
		foreach ($this->_read_crontab() as $line) {
			if (strpos($line, $command) !== FALSE) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Return all current cron job lines.
	 *
	 * @return array
	 */
	public function list_all()
	{
		return $this->_read_crontab();
	}

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

	/**
	 * Read the current user's crontab into an array of lines.
	 *
	 * @return array
	 */
	private function _read_crontab()
	{
		exec('crontab -l 2>/dev/null', $lines, $returnCode);

		// Return code 1 with no output = empty crontab (not an error)
		return ($returnCode === 0 || empty($lines)) ? $lines : [];
	}

	/**
	 * Write an array of cron lines to the crontab.
	 *
	 * @param  array $lines
	 * @return bool
	 */
	private function _write_crontab(array $lines)
	{
		$content = implode("\n", $lines) . "\n";
		$tmpFile = tempnam(sys_get_temp_dir(), 'cron_');

		if ($tmpFile === FALSE) {
			return FALSE;
		}

		file_put_contents($tmpFile, $content);
		exec("crontab " . escapeshellarg($tmpFile), $output, $returnCode);
		unlink($tmpFile);

		return $returnCode === 0;
	}

	/**
	 * Build a full crontab line from expression and command.
	 *
	 * @param  string $cronExpression
	 * @param  string $command
	 * @return string
	 */
	private function _build_line($cronExpression, $command)
	{
		return trim($cronExpression) . ' ' . trim($command);
	}
}
