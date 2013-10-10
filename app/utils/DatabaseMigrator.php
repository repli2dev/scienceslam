<?php
use Nette\Database\Connection;
use Nette\Database\SqlLiteral;
use Nette\Object;
use Nette\Utils\Strings;

/**
 * Class that performs database migration (all SQL scripts from given path)...
 * 1. Sort SQL scripts from given path by name (prefix them with 001 to ensure correct order)
 * 2. Perform all scripts which are not present in database table migrations. Each is done in own trasaction
 */
final class DatabaseMigrator extends Object {

	const MIGRATION_TABLE_NAME = "meta_migrations";

	/** @var Connection */
	private $connection;
	private $path;

	/**
	 * @param Connection $connection Database connection
	 * @param $path Real absolute path where SQL scripts are stored
	 */
	public function __construct(Connection $connection, $path) {
		$this->connection = $connection;
		$this->path = $path;
	}

	private function getFiles() {
		$output = array();
		if ($handle = opendir($this->path)) {
			while (false !== ($entry = readdir($handle))) {
				if (Strings::endsWith($entry, '.sql')) {
					$output[] = $entry;
				}
			}
			closedir($handle);
		}
		asort($output);
		return $output;
	}

	private function checkMigrationTable() {
		$rows = $this->connection->query('SHOW TABLES LIKE ?', self::MIGRATION_TABLE_NAME);
		if($rows->rowCount() == 0) {
			$this->connection->query('
				CREATE TABLE ? (file VARCHAR(255) NOT NULL, UNIQUE(file))
			', new SqlLiteral(self::MIGRATION_TABLE_NAME));
		}
	}

	public function migrate() {
		echo "Migration begins\n";
		echo "----------------\n";
		$this->checkMigrationTable();
		$files = $this->getFiles();
		foreach($files as $file) {
			$processed = $this->connection->query('SELECT COUNT(*) FROM ? WHERE file = ?', new SqlLiteral(self::MIGRATION_TABLE_NAME), $file)->fetchAll();
			if($processed[0][0] > 0) {
				echo "Skipping already processed file: ". $file."\n";
				continue;
			}
			echo "Processing: ". $file."\n";
			$content = file_get_contents($this->path . '/' . $file);
			$content .= " INSERT INTO ". self::MIGRATION_TABLE_NAME . " (file) VALUES ('" . $file ."');";
			try {
				$this->connection->beginTransaction();
				$this->connection->exec($content);
				$this->connection->commit();
			} catch (PDOException $ex) {
				echo "FAIL: ". $file . "\n";
				echo "Error message: " . $ex->getMessage() . "\n";
				echo $ex->getTraceAsString();
				$this->connection->rollBack();
			} catch (Exception $ex) {
				echo "FAIL: ". $file . "\n";
				echo $ex->getTraceAsString();
				$this->connection->rollBack();
			}
		}
	}
}