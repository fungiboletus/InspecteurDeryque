<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Interface to manage different file sources when loading datas.
 */
interface FileType {
	/** Check file's data type. */
	public static function isOfThisDataType($file,$extension);
	/** Display a form listing importable data from the incoming file. */
	public static function getImportableData($file);
	/** Store selection into the database. */
	public static function submitSelection($data);
	
}

?>
