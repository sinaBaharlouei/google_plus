<?php
class Application_Model_ParentMapper {

	protected $_dbTable;

	public function setDbTable()
	{
		$tempTable = new $this->_dbTableName();
		if (!($tempTable instanceof Zend_Db_Table_Abstract)) {
			throw new Exception('Invalid table data gateway provided');
		}

		$this->_dbTable = $tempTable;
		return $this;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable();
		}
		return $this->_dbTable;
	}

	public function find($id)
	{
		$result = $this->getDbTable()->find($id);
		if (count($result) == 0) {
			return null;
		}

		$row = $result->current();
		return $row->toArray();
	}

	public function delete($id)
	{
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
		return $this->getDbTable()->delete($where);
	}

	/**
	 *
	 * Only returns one of the results! Use find findAllByColumn to get more than one result.
	 * @param String $column
	 * @param unknown_type $query_value
	 * @return  NULL|array
	 */
	function findByColumn($column, $query_value)
	{
		$select = $this->getDbTable()->select()->where("$column = ?", $query_value);
		$resultSet = $this->getDbTable()->fetchAll($select);

		if (count($resultSet) == 0) {
			return null;
		}

		$row = $resultSet->current();
		return $row->toArray();
	}

	function findByTwoColumns($column1, $query_value1, $column2, $query_value2)
	{
		$select = $this->getDbTable()->select();
		$select->where("$column1 = ?", $query_value1);
		$select->where("$column2 = ?", $query_value2);

		$resultSet = $this->getDbTable()->fetchAll($select);

		if (count($resultSet) == 0) {
			return null;
		}

		$row = $resultSet->current();

		return $row->toArray();
	}

	function findAllByColumn($column, $query_value)
	{
		$select = $this->getDbTable()->select()->where("$column = ?", $query_value);
		$result_set = $this->getDbTable()->fetchAll($select);

		if(count($result_set) < 1)
			return null;
		return $result_set;
	}

	function findAllByTwoColumns($column1, $query_value1, $column2, $query_value2)
	{
		$select = $this->getDbTable()->select();
		$select->where("$column1 = ?", $query_value1);
		$select->where("$column2 = ?", $query_value2);

		$result_set = $this->getDbTable()->fetchAll($select);

		if(count($result_set) < 1)
			return null;
		return $result_set;
	}

	public function save(&$model)
	{
		$data = array();
		foreach($model->_fields as $key => $value)
			$data[$key] = $value;


		if (null === $data['id']) {

			if(array_key_exists('created_at', $model->_fields))
				$data['created_at'] = time();
			unset($data['id']);
			$new_id = $this->getDbTable()->insert($data);
			$model->_fields['id'] = $new_id;
			return intval($new_id);
		} else {
			foreach($data as $key=>$value)
				if($data[$key] == null)
					unset($data[$key]);
			$this->getDbTable()->update($data, array('id = ?' => $data['id']));
			return $data['id'];
		}
	}

}