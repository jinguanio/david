<?php
class em_alarm_property_abstract
{
	// {{{ members

	/**
	 * 允许设置的属性 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__allow_attributes = array();

	/**
	 *  存储属性值的数组 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__attributes = array();
	
	/**
	 * 允许设置的其他属性对象 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__allow_propertys = array();

	/**
	 * 存储属性的对象 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__propertys = array();

	/**
	 * 允许序列化的属性 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__allow_serialize = array();

	// }}}
	// {{{ functions
	// {{{ public function __construct()

	/**
	 * __construct 
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(array $attributes = array())
	{
		if (empty($attributes)) {
			return;
		}

		foreach ($this->__allow_attributes as $attribute => $value) {
			if (array_key_exists($attribute, $attributes)) {
				$set = 'set_' . $attribute;
				if (method_exists($this, $set)) {
					$this->$set($attributes[$attribute]);
				} else {
					$this->__attributes[$attribute] = $attributes[$attribute];
				} 
			}
		}

		foreach ($this->__allow_propertys as $property => $value) {
			if (array_key_exists($property, $attributes)) {
				$object = 'em_member_property_' . $property;
				if ($attributes[$property] instanceof $object) {
					$this->__propertys[$property] = $attributes[$property];
				} else {
					require_once PATH_EYOUM_LIB . 'member/property/em_member_property_exception.class.php';
					throw new em_member_property_exception("The value of $property must be '$object' object!");
				}
			}
		}	
	}

	// }}}
	// }}}	
}
