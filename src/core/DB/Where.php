<?php 
namespace ke\DB;

use ke\Exception;

class Where
{
	private $where='';
	private $bind=[];
	public function __construct($where)
	{
		$this->where=$this->parse($where);
	}

	public function parse($where)
	{
		$tmp='';
		foreach ($where as $key=>$value) {
			if(is_array($value)){
				$tmp.=' AND ('.$this->parse($value).')';
			}else{
				$col='';
				if(preg_match('/(\[(?P<cond>and|or)\])*(?P<column>[a-zA-Z0-9]+)(\[(?P<eq>.+?)\])*/',$key,$match)){
					$eq=isset($match['eq']) ? $match['eq'] : '=';
					$cond=isset($match['cond']) && $match['cond']!='' ? strtoupper($match['cond']) : 'AND';
					if($tmp!=''){
						$tmp.=' '.$cond.' ';
					}
					$tmp.="`{$match['column']}` {$eq} :where_{$match['column']}";
					$col=$match['column'];
				}
				if($col==''){
					throw new Exception('Mysql Column is null');
				}
				$this->bind['where_'.$col]=$value;
			}
		}
		return $tmp;
	}

	public function parseWhere()
	{
		return $this->where;
	}
	public function parseBind()
	{
		return $this->bind;
	}

	/**
	 * 获取数组维度
	 * @return int
	 */
	public function getArrayLevel($array)
	{
		$n=1;
		foreach ($array as $item) {
			if(is_array($item)){
				$n=$this->getArrayLevel($item)+1;
			}
		}
		return $n;

	}

}