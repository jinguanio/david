<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * em_explain_db_schema.class.php
 *
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id: em_explain_db_schema.class.php 5796 2009-06-18 08:45:15Z hebingchun $
 */

/* requires */
require_once 'conf_path_devmail.php';
require_once EMP_PATH_LIB.'em_smarty.class.php';

// {{{ TPLS
// {{{ TPL_DATABASE
//数据库的模板
$TPL_DATABASE = <<<TPLDATABASE
--
-- Current Database: `{{db_name}}`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `{{db_name}}` /*!40100 DEFAULT CHARACTER SET {{db_charset}} */;

USE `{{db_name}}`;


TPLDATABASE;
// }}}

// {{{ TPL_CREATION_TABLE
//数据库表的模板
$TPL_CREATION_TABLE = <<<TPLCREATIONTABLE
--
-- Table structure for table `{{table_name}}`
--

CREATE TABLE `{{table_name}}` (
{{columns}}{{keys}}
) ENGINE={{table_engine}} DEFAULT CHARSET={{table_charset}};


TPLCREATIONTABLE;
// }}} 

// {{{ TPL_DESC_TABLE   
//表描述的模板
$TPL_DESC_TABLE = <<<TPLDESCTABLE
-- {{{ table {{table_name}}

--
-- {{table_desc}}
--

TPLDESCTABLE;
// }}}

// {{{ TPL_FIELDS   
//字段描述模板
$TPL_FIELDS = <<<TPLFIELDS
-- {{field}}
--    {{desc}}

TPLFIELDS;
// }}}

// {{{ TPL_TABLE
// 创建表的模板
$TPL_TABLE = <<<TPLTABLE
--

CREATE TABLE `{{table_name}}` (
{{columns}}{{keys}}
) ENGINE={{table_engine}} DEFAULT CHARSET={{table_charset}};

-- }}}

TPLTABLE;
// }}}

// {{{ TPL_DESC_TOP
$TPL_DESC_TOP = <<<TPLDESCTOP
-- vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:


TPLDESCTOP;
// }}}

// {{{ TPL_FIELD_ARRAY
$TPL_FIELD_ARRAY = <<<TPLFIELDARRAY
            '{{field_name}}' => array('type' => \${{field_type}}, 'default' => {{field_default}}, {{field_max_min}}, {{field_in}}),

TPLFIELDARRAY;
// }}}


// }}} end of tpls

// {{{ defines
//模板的定义
define('EM_TPL_DATABASE', $TPL_DATABASE);
define('EM_TPL_DESC_TOP', $TPL_DESC_TOP);
define('EM_TPL_DESC_TABLE', $TPL_DESC_TABLE);
define('EM_TPL_CREATION_TABLE', $TPL_CREATION_TABLE);
define('EM_TPL_FIELDS', $TPL_FIELDS);
define('EM_TPL_TABLE', $TPL_TABLE);
define('EM_TPL_FIELD_ARRAY', $TPL_FIELD_ARRAY);

//全局配置
define('EM_XML_NAME', EMP_PATH_LIB . 'db_schema/db_schema.xml');
define('EM_DESCRIPTION_NAME', 'db_desc_');    //生成表描述sql文件名
define('EM_CREATION_NAME', 'db_creation_');   //生成创建表sql文件名
// }}}

/**
 * em_explain_db_schema.class.php 解析XML文件
 * 
 * @version 1.0
 * @copyright Beijing eyou Infomaition Technology Co., Ltd.
 * @author Hebingchun <hebingchun@eyou.net> 
 * @date   2008-10-08
 */
class em_explain_db_schema
{
    // {{{ consts 
    const EID_NULLXML  = '001001';
    const EID_OPENFILE = '001002';
    const EID_FWRITER  = '001003';
    const EID_NULLFILE = '001004';
    const EID_OUTPUT   = '001005';
    const EID_XMLOBJ   = '001006';
    // }}}

    // {{{ members
    /**
     *  数据库名
     *
     */
    private $__db_name;

    /**
     *数据库字符集
     * 
     */
    private $__db_charset;

    /**
     * 表名
     * 
     */
    private $__table_name;

    /**
     * 表的引擎
     * 
     */
    private $__table_engine;

    /**
     * 表的字符集
     * 
     */
    private $__table_charset;

    /**
     * simpleXml 的对象 
     * 
     */
    private $__obj_xml;

    /**
     * XML 文件的路径
     * 
     */
    private $__xml_file;

    /**
     * 是否生成html文件 
     * 
     */
    private $__is_out_html = false;

    /**
     * html 文档的路径 
     * 
     * @var string
     */
    private $__doc_path = './';

    /**
     * 生成表描述文件的路径 
     * 
     * @var string
     */
    private $__desc_path = './';

    /**
     * 生成的创建库的sql文件路径 
     * 
     * @var string
     */
    private $__creation_path = './';

    /**
     * 生成的 html 中 title 的项目名称
     * 
     * @var string
     */
    private $__project_name = 'Docs';

    /**
     * 项目关键字
     * 
     * @var string
     */
    private $__project_key = null;

    /**
     * 错误信息
     * 
     * @var array
     */
    private $__error_info = array('code' => null, 'msg' => null);

    // }}} end of members

    // {{{ functions
    // {{{ function set_project_name

    /**
     * 设置生成的 html 中 title 的项目名称
     * 
     * @param string $project_name 项目名称
     * @return void
     */
    public function set_project_name($project_name)
    {
        $this->__project_name = $project_name;
    }

    // }}}
    // {{{ function set_project_key

    /**
     * 设置项目关键字
     * 
     * @param string $project_key 项目关键字
     * @return void
     */
    public function set_project_key($project_key)
    {
        $this->__project_key = $project_key;
    }

    // }}}
    // {{{ function _set_db_name

    /**
     * 设置数据库的名字 
     * 
     * @param string $db_name 数据库名
     * @return void
     */
    private function _set_db_name($db_name)
    {
        $this->__db_name = $db_name;
    }

    // }}}
    // {{{ private function _set_db_character

    /**
     * 设置数据库的字符集 
     * 
     * @param string $db_character  数据库字符集
     * @return void
     */
    private function _set_db_character($db_character)
    {
        $this->__db_character = $db_character;
    }

    // }}}
    // {{{ private function _get_tpl

    /**
     * 初始化 tpl 对象
     * 
     * @return void
     */
    private function _get_tpl() 
    {
        $tpl_path = EMP_PATH_TPL . 'dbdoc/'; 
        $com_path = EMP_PATH_TPLC;
        $com_id   = 'db_schema';

        return new em_smarty($tpl_path, $com_path, $com_id);
    }

    // }}}
    // {{{ private function _set_table_name

    /**
     * 设置数据库表名 
     * 
     * @param string $table_name 表名
     * @return void
     */
    private function _set_table_name($table_name) 
    {
        $this->__table_name = $table_name;
    }

    // }}}
    // {{{ private function _set_table_engine

    /**
     * 设置数据库表的引擎 
     * 
     * @param string $table_engine 表引擎
     * @return void
     */
    private function _set_table_engine($table_engine)
    {
        $this->__table_engine = $table_engine;
    }

    // }}}
    // {{{ private function _set_table_charset

    /**
     * 设置数据库表的字符集 
     * 
     * @param string $table_charset  表的字符集
     * @return void
     */
    private function _set_table_charset($table_charset)
    {
        $this->__table_charset = $table_charset;
    }

    // }}}
    // {{{ private function _set_error_info

    /**
     * 设置错误信息 
     * 
     * @param string $id   错误代码
     * @param string $msg  错误信息
     * 
     * @return void
     */
    private function _set_error_info($code, $msg)
    {
        $this->__error_info = array('code' => $id , 'msg' => $msg);
    }

    // }}}
    // {{{ public function get_error_info

    /**
     * 获取错误信息 
     * 
     * @return array
     */
    public function get_error_info()
    {
        return $this->__error_info;
    }

    // }}}
    // {{{ private function _create_database

    /**
     * 创建数据库 sql 语句
     * 
     * @return string     创建数据库的sql语句字符串
     */
    private function _create_database()
    {
        // 替换的标记,是替换数据库(EM_TPL_DATABASE)，表(EM_TPL_CREATION_TABLE)还是其他
        $flag = 'EM_TPL_DATABASE';
        $db_info = 
            array(
                'db_name'   => $this->__db_name,
                'db_charset'=> $this->__db_character,
            );

        return $this->_replace_flag($db_info, $flag);
    }

    // }}}

    // {{{ public function set_is_out_html

    /**
     * set_is_out_html 
     * 
     * @param bool $is_out_html  是否允许生成html文件
     * 
     * @return void
     */
    public function set_is_out_html($is_out_html=null)  
    {
        if ($is_out_html === null) {
            $this->__is_out_html = false;
        } else {
            $this->__is_out_html = $is_out_html;
        }
    }

    // }}}
    // {{{ private function __construct

    /**
     * 构造函数, 设置xml文件的路径，设置simpleXml的对象
     * 
     * @param string $xml_file  解析的xml文件的路径
     * @return void
     */
    public function __construct($xml_file=null)
    {
        if ($xml_file === null) {
            $xml_file = EM_XML_NAME; 
        }

        if (is_file($xml_file)) {
            $this->__xml_file = $xml_file;
            $this->__obj_xml = simplexml_load_file($this->__xml_file);
        }
    }

    // }}}
    // {{{ public function _check_xml_obj()

    /**
     * 验证 xml object 的合法性
     * 
     * @return boolean
     */
    private function _check_xml_obj()
    {
        if (!$this->__obj_xml) {
            $this->_set_error_info(self::EID_XMLOBJ, "create xml object error");
            return false;
        }
        return true;;
    }

    // }}}
    // {{{ public function set_out_path

    /**
     * 设置输出的路径, 默认为当前目录 
     * 
     * @param array $out_path  doc, creation, desc 的输出路径
     * 
     * @return void
     */
    public function set_out_path($out_path)
    {
        if (isset($out_path['doc'])) {
            $this->__doc_path = rtrim($out_path['doc'], '/') . '/';
        }
        if (isset($out_path['desc'])) {
            $this->__desc_path = rtrim($out_path['desc'], '/') . '/';
        }
        if (isset($out_path['creation'])) {
            $this->__creation_path = rtrim($out_path['creation'], '/') . '/';
        }
    }

    // }}}
    // {{{ private function _get_attributes

    /**
     * 获取一个标签的属性数组 
     * 
     * @param object $obj_attributes  simpleXml 对象
     * @param string $name 属性的名字 
     * 
     * @return array 所有属性的数组 | 指定的属性值
     */
    private function _get_attributes($obj_attributes, $name = null) 
    {
        $array_attributes = array();
        foreach ($obj_attributes as $key => $value) {
            $array_attributes[$key] = $value;
        }

        if (null === $name) {
            return $array_attributes;
        } else {
            return isset($array_attributes[$name]) ? $array_attributes[$name] : null;
        }

    }

    // }}}
    // {{{ private function _replace_flag

    /**
     * 模板的替换 
     * 
     * @param array $replace  要替换的变量数组,替换数据库模板
     * @param mixed $flag     替换的标记，是替换数据库(EM_TPL_DATABASE)、表(EM_TPL_CREATION_TABLE)还是其他
     *
     * @return string   替换完的sql语句字符串
     */
    private function _replace_flag($replace, $flag) 
    {
        switch ($flag) {
            case 'EM_TPL_DATABASE':
                $tpl_str = str_replace("{{db_name}}", $replace['db_name'], EM_TPL_DATABASE);
                $tpl_str = str_replace("{{db_charset}}", $replace['db_charset'], $tpl_str);
                break;

            case 'EM_TPL_CREATION_TABLE':
                $tpl_str = str_replace("{{table_name}}", $replace['table_name'], EM_TPL_CREATION_TABLE);
                $tpl_str = str_replace("{{columns}}", $replace['columns'], $tpl_str);
                $tpl_str = str_replace("{{keys}}", $replace['keys'], $tpl_str);
                $tpl_str = str_replace("{{table_engine}}", $replace['table_engine'], $tpl_str);
                $tpl_str = str_replace("{{table_charset}}", $replace['table_charset'], $tpl_str);
                break;

            case 'EM_TPL_DESC_TABLE':
                // 描述出现换行的情况，可以如下处理
                $replace['table_desc'] = str_replace("\n", "\n--    ", $replace['table_desc']);
                $tpl_str = str_replace("{{table_desc}}", $replace['table_desc'], EM_TPL_DESC_TABLE);
                $tpl_str = str_replace("{{table_name}}", $replace['table_name'], $tpl_str);
                break;

            case 'EM_TPL_FIELDS':
                $tpl_str = str_replace("{{field}}", $replace['field'], EM_TPL_FIELDS);
                // 描述出现换行的情况，可以如下处理
                $replace['desc'] = str_replace("\n", "\n--    ", $replace['desc']);
                $tpl_str = str_replace("{{desc}}", $replace['desc'], $tpl_str);
                break;

            case 'EM_TPL_TABLE':
                $tpl_str = str_replace("{{table_name}}", $replace['table_name'], EM_TPL_TABLE);
                $tpl_str = str_replace("{{columns}}", $replace['columns'], $tpl_str);
                $tpl_str = str_replace("{{keys}}", $replace['keys'], $tpl_str);
                $tpl_str = str_replace("{{table_engine}}", $replace['table_engine'], $tpl_str);
                $tpl_str = str_replace("{{table_charset}}", $replace['table_charset'], $tpl_str);
                break;

            case 'EM_TPL_FIELD_ARRAY':
                $tpl_str = str_replace("{{field_name}}", $replace['field_name'], EM_TPL_FIELD_ARRAY);
                $tpl_str = str_replace("{{field_type}}", $replace['field_type'], $tpl_str);
                $tpl_str = str_replace("{{field_default}}", $replace['field_default'], $tpl_str);
                $tpl_str = str_replace("{{field_max_min}}", $replace['field_max_min'], $tpl_str);
                $tpl_str = str_replace("{{field_in}}", $replace['field_in'], $tpl_str);
                break;

            default:
                $tpl_str = '';
                break;
        }

        return $tpl_str;
    }

    // }}}
    // {{{ private function _write_file

    /**
     * 写文件
     * 
     * @param string $file_content  文件的内容
     * @param string $file_name  文件名
     * 
     * @return boolean
     */
    private function _write_file($file_content, $file_name)
    {
        if (!$fp = @fopen($file_name, 'wr')) {
            $this->_set_error_info(self::EID_OPENFILE, "OPEN FILE($file_name) ERROR!");
            return false;
        }
        if (false === fwrite($fp, $file_content)) {
            $this->_set_error_info(self::EID_FWRITER, "FWRITE FILE($file_name) ERROR!");
            fclose($fp);
            return false;
        }
        fclose($fp);
        return true;
    }

    // }}}
    // {{{ private function _get_db_tables

    /**
     * 获取所有的数据库和表数组 
     * 
     * @return array 表的数组
     */
    private function _get_db_tables() 
    {
        if (!$this->_check_xml_obj()) {
            return false;
        }

        $key_db = 0;
        foreach ($this->__obj_xml->database as $db_info) {
            $db_name = $this->_get_attributes($db_info->attributes(), 'name');
            $array_tables[$key_db]['name'] = $db_name;

            foreach ($db_info->tables->table as $table_info) 
            {
                $multisplit = $table_info->multisplit;
                $table_name = $this->_get_attributes($table_info->attributes(), 'name');
                //获取分表的主表
                if ($multisplit == "true") {
                    $table_name = $this->_split_table_num($table_name);
                }
                $array_tables[$key_db]['tables'][] = $table_name;
            }
            $key_db++;
        }

        return $array_tables;
    }

    // }}}
    // {{{ private function _get_multipl_column_out_html 

    /**
     * 获取表字段的描述信息，创建语句，字段名
     * 
     * @param obj $columns  simpleXml 对象
     * 
     * @return array   字段信息数组
     */
    private function _get_multiple_column_out_html($columns)
    {
        $key_index = 0;
        if ($columns) {
            foreach ($columns->column as $field_info) 
            {
                $array_filed_attributes = array();
                foreach ($field_info->children() as $key => $value) {
                    $array_filed_attributes[$key] = (string) $value;
                }
                $field_type = $field_info->type;
                $field_name = $this->_get_attributes($field_info->attributes(), 'name');
                $field_desc = '<pre>' . trim(htmlspecialchars($field_info->desc)) . "</pre>";
                $default   = null;
                $charset   = null;
                $collate   = $field_info->collate;
                $nullable  = $field_info->nullable;
                $precision = $field_info->precision;
                if (isset($array_filed_attributes['charset'])) {
                    $charset = $field_info->charset;
                }
                if (isset($array_filed_attributes['default'])) {
                    $default = $field_info->default;
                }
                $create_str = $this->_set_one_column_out_html($field_type, $precision, $nullable, $default, $collate, $charset);
                $array_field[$key_index]['field_name'] = $field_name;
                $array_field[$key_index]['statement']  = $create_str;
                $array_field[$key_index]['description'] = $field_desc;
                $key_index++;
            }
        }
        return $array_field;
    }

    // }}}
    // {{{ private function _set_one_column_out_html

    /**
     * 设置一个列的 html 输出内容
     * 
     * @param string $type  字段类型
     * @param string $precision     字段的精度
     * @param string $nullable  字段是否为null
     * @param string $default   字段的默认值
     * @param string $collate   
     * @param string $charset   字段的字符集
     * 
     * @return string   字符串
     */
    private function _set_one_column_out_html($type, $precision, $nullable, $default, $collate = null, $charset = null) 
    {
        $str_null = null;
        $str_precision = null;
        $str_collate = null;
        $str_default = null;
        $str_auto_increment = null;
        $str_charset = null;

        if ('false' == $nullable) {
            $str_null = ' NOT NULL';
        }
        if ('' != $precision) {
            $str_precision = "($precision)";
        }
        if ('' != $collate) {
            $charset = (string)$this->__table_charset;
            $str_collate = " COLLATE $collate";
        }
        if ('' != $charset) {
            $str_charset = " CHARACTER SET $charset"; 
        }

        // 如果列类型是 autoint, autobigint,autotinyint, 则设置 AUTO_INCREMENT
        if (preg_match("/^auto/", $type)) {
            $str_auto_increment = ' AUTO_INCREMENT';
            $type = substr($type, 4);
        } else {
            if ('NULL' === strtoupper($default)) {
                $str_default = " DEFAULT $default";
            } else if (null !== $default) {
                $str_default = " DEFAULT '$default'";
            }
        }

        $create_str = $type 
                    . $str_precision 
                    . $str_charset
                    . $str_collate
                    . $str_null 
                    . $str_auto_increment
                    . $str_default
                    . ",\n";

        return $create_str;
    }

    // }}}
    // {{{ private function _get_multiple_key_out_html

    /**
     * 获取多个 Key 的输出字符串
     * 
     * @param object $keys  simpleXml 对象
     * 
     * @return sting 字符串
     */
    private function _get_multiple_key_out_html($keys)
    {
        $array_keys = array();

        if ($keys) 
        {
            $key_index = 0;
            foreach ($keys->key as $key_field) {
                $fields   = $key_field->fields;
                $key_type = $key_field->type;
                $key_desc = '<pre>' . trim(htmlspecialchars($key_field->desc)) . '</pre>';
                //echo "desc = $key_desc \n";
                $key_name = $this->_get_attributes($key_field->attributes(), 'name');
                $create_str = $this->_set_one_key_out_html($key_name, $key_type, $fields);

                $array_keys[$key_index]['key_name'] = $key_name;
                $array_keys[$key_index]['key_type'] = $key_type;
                $array_keys[$key_index]['key_field'] = $create_str;
                $array_keys[$key_index]['key_desc'] = $key_desc;
                $key_index++;
            }
        }
        return $array_keys;
    }

    // }}}
    // {{{ private function _set_one_key_out_html

    /**
     * 设置一个约束的html输出
     * 
     * @param string $key_name   约束名 
     * @param string $key_type   约束类型
     * @param string $fields     约束字段
     *
     * @return string 字符串
     */
    private function _set_one_key_out_html($key_name, $key_type, $fields)
    {
        $array_fields = $array_len = array();
        foreach ($fields->field as $field_info) {
            $array_fields[] = $this->_get_attributes($field_info->attributes(), 'name');
            $array_len[] = $this->_get_attributes($field_info->attributes(), 'length');
        }
        $field_num = count($array_fields);

        $key_type = strtoupper($key_type);
        switch ($key_type) {
            case 'UNIQUE':
                $create_str = "{$key_name} (". implode(', ', $array_fields). ")";
                break;

            case 'INDEX':
                $num_fields = count($array_fields);
                $array_fields_str = array();
                for ($i = 0; $i < $num_fields; $i++) {
                    if (isset($array_len[$i])) {
                        $array_fields_str[] = "{$array_fields[$i]}({$array_len[$i]})";
                    } else {
                        $array_fields_str[] = "{$array_fields[$i]}";
                    }
                }
                $create_str = "{$key_name} (". implode(',', $array_fields_str). "),\n";
                break;

            case 'PRIMARY':
                $create_str = "(". implode(',', $array_fields). ")";
                break;

            default:
                $create_str = '';
                break;
        }
        return $create_str;
    }

    // }}}
    // {{{ private function _get_mulitple_table_out_html

    /**
     * 获取多个表的静态输出字符串,用于生成html帮助 
     * 
     * @param object $obj_tables  simpleXml 对象
     * 
     * @return sting 字符串
     */
    private function _get_mulitple_table_out_html($obj_tables)
    {
        $key_index = 0;
        if ($obj_tables) {
            foreach ($obj_tables->table as $table_info)  {
                $table_name = $this->_get_attributes($table_info->attributes(), 'name');
                $multisplit    = $table_info->multisplit;
                if ($multisplit == "true")  {
                    $table_name = $this->_split_table_num($table_name);
                }
                $this->_set_table_name($table_name);
                $this->_set_table_charset($table_info->charset);
                $this->_set_table_engine($table_info->engine);

                
                $table_columns = $table_info->columns;
                $table_keys    = $table_info->keys;
                $table_desc    = $table_info->desc;
                $out_html[$key_index] = $this->_get_one_table_out_html($multisplit, $table_columns, $table_keys, $table_desc);
                $key_index++;
            }
        }
        return $out_html;
    }

    // }}}
    // {{{ private function _get_one_table_out_html

    /**
     * 获取一个表的静态输出，用于生成html帮助 
     * 
     * @param string $multisplit 是否分表
     * @param object $columns    列对象
     * @param objecy $keys   约束对象
     * 
     * @return string     字符串
     */
    private function _get_one_table_out_html($multisplit, $columns, $keys, $desc=null)
    {
        $array_out_html['name'] = $this->__table_name;
        $array_out_html['charset'] = $this->__table_charset;
        $array_out_html['engine'] = $this->__table_engine;
        $array_out_html['desc'] = '<pre>' . trim(htmlspecialchars($desc)) . "</pre>";
        if ($multisplit == "" or $multisplit == 'false') {
            $array_out_html['multisplit'] = '否';
        } else {
            $array_out_html['multisplit'] = '是';
        }
        $array_out_html['columns'] = $this->_get_multiple_column_out_html($columns);
        $array_out_html['keys'] = $this->_get_multiple_key_out_html($keys);

        return $array_out_html;
    }

    // }}}
    // {{{ private function _get_index_out_html

    /**
     * 获取 index.html 的内容 
     * 
     * @return string
     */
    private function _get_index_out_html()
    {
    }

    // }}}

    // {{{ private function _set_one_column

    /**
     * 设置一个字段的sql语句 
     * 
     * @param string $field  字段名
     * @param string $type   字段的类型
     * @param string $precision  字段的精度
     * @param string $nullable   字符是否为NULL
     * @param string $default    字段的默认值
     * @param string $collate    
     * @param string $charset    字段的字符集
     *
     * @return string  一个字段完整的sql语句
     */
    private function _set_one_column($field, $type, $precision, $nullable, $default, $collate=null, $charset = null) 
    {
        $str_null = null;
        $str_precision = null;
        $str_collate = null;
        $str_default = null;
        $str_auto_increment = null;
        $str_charset = null;

        if ('false' == $nullable) {
            $str_null = ' NOT NULL';
        }
        if ('' != $precision) {
            $str_precision = "($precision)";
        }
        if ('' != $collate) {
            $charset = (string)$this->__table_charset;
            $str_collate = " COLLATE $collate";
        }
        if ('' != $charset) {
            $str_charset = " CHARACTER SET $charset"; 
        }

        // 如果列类型是 autoint, autobigint,autotinyint, 则设置 AUTO_INCREMENT
        if (preg_match("/^auto/", $type)) {
            $str_auto_increment = ' AUTO_INCREMENT'; 
            $type = substr($type, 4);
        } else {
            if ('NULL' === strtoupper($default)) {
                $str_default = " DEFAULT $default";
            } else if (null !== $default) {
                $str_default = " DEFAULT '$default'";
            }
        }

        $create_str = "  `{$field}` $type" 
                    . $str_precision
                    . $str_charset
                    . $str_collate
                    . $str_null
                    . $str_auto_increment
                    . $str_default
                    . ",\n";

        return $create_str;
    }

    // }}}
    // {{{ private function _set_multiple_column

    /**
     * 设置多个字段的sql语句
     * 
     * @param array $columns  simplexml obj 
     * @param mixed $charset  字段的字符集
     * 
     * @return string   多个字段完成的sql语句字符串
     */
    private function _set_multiple_column($columns, $charset=null)
    {
        if ($columns) {
            $create_str = '';
            foreach ($columns->column as $field_info) 
            {
                // 一个字段的属性数组
                $array_filed_attributes = array();
                foreach ($field_info as $key => $value) {
                    $array_filed_attributes[$key] = (string)$value;
                }
                $default   = null;
                $charset   = null;

                $field     = $this->_get_attributes($field_info->attributes(), 'name');
                $type      = $field_info->type;
                $collate   = $field_info->collate;
                $nullable  = $field_info->nullable;
                $precision = $field_info->precision;

                if (isset($array_filed_attributes['charset'])) {
                    $charset = $field_info->charset;
                } 
                if (isset($array_filed_attributes['default'])) {
                    $default = $field_info->default;
                }

                $create_str .= $this->_set_one_column($field, $type, $precision, $nullable, $default, $collate, $charset);
            }
        }
        return $create_str;
    }

    // }}}

    // {{{ private function _set_one_column_desc

    /**
     * 创建一个列的描述
     *
     * @param string $field_name 字段名
     * @param string $field_desc 字段的描述
     *
     * @return string 一个字段描述的字符串
     */
    private function _set_one_column_desc($field_name, $field_desc)
    {
        $field_flag = 'EM_TPL_FIELDS';
        $field_info = 
            array(
                'field' => $field_name,
                'desc'  => $field_desc,
            );

        $desc = $this->_replace_flag($field_info, $field_flag);

        return $desc;
    }

    // }}}
    // {{{ private function _set_one_column_array

    /**
     * 把一个字段格式化成一个数组
     * 
     * @param string $field_name  列名
     * @param string $field_type   列类型
     * @param string $field_default  列的默认值
     * @param string $field_lmax     列的最大值
     * @param string $field_lmin     列的最小值
     * @param string $field_in       列的in值
     * 
     * @return void 格式化完的字符串
     */
    private function _set_one_column_array($field_name, $field_type, $field_default, $field_lmax=null, $field_in=null)
    {
        $flag = 'EM_TPL_FIELD_ARRAY';
        if ($field_in == "") {
            $field_in = "'in' => array()";
        } else {
            $field_in = "'in' => array($field_in)";
        }
        if ($field_default == "") {
            $field_default = "''";
        }
        //格式化字段类型
        switch ($field_type) {
            case 'varchar':
            case 'char':
                $field_type = 't_int';
                $field_max_min = "'lmax' => $field_lmax, 'lmin' => 0";
                break;
            case 'int':
            case 'tinyint':
            case 'bigint':
                $field_type = 't_str';
                $field_max_min = "'lmax' => $field_lmax";
                break;
            default:
                $field_type = '';
                break;
        }
        $field_info = 
            array(
                'field_name' => $field_name,
                'field_type' => $field_type,
                'field_default' => $field_default,
                'field_max_min' => $field_max_min,
                'field_in'   => $field_in,
            ); 
        $field_array = $this->_replace_flag($field_info, $flag);

        return $field_array;
    }

    // }}}
    // {{{ private function _set_multiple_column_array

    /**
     * 把多个列格式化成多个数组 
     * 
     * @param object $columns  simpleXml 对象
     * 
     * @return string 多个列的数组字符串
     */
    private function _set_multiple_column_array($columns)
    {
        if ($columns) {
            foreach ($columns->column as $column_info) {
                $field_name = $this->_get_attributes($column_info->attributes(), 'name');
                $field_type = $column_info->type;
                $field_max_min = $column_info->precision;
                $field_in   = $column_info->in;
                $field_default = $column_info->default;
                $field_arrays .= $this->_set_one_column_array($field_name, $field_type, $field_default, 
                                                              $field_max_min, $field_in);
            }
        }
        return $field_arrays;
    }

    // }}}
    // {{{ private function _set_multiple_column_desc

    /**
     * 设置多个列的描述 
     * 
     * @param object $columns  simplexml 对象
     *
     * @return string 多个列描述的字符串
     */
    private function _set_multiple_column_desc($columns)
    {
        if ($columns) {
            foreach ($columns->column as $field_info) {
                $field_desc = $field_info->desc;
                $field_name = $this->_get_attributes($field_info->attributes(), 'name');

                $create_str .= $this->_set_one_column_desc($field_name, $field_desc);
            }
        }
        return $create_str;
    }

    // }}}
    // {{{ private function _set_one_key

    /**
     * 设置一个约束
     * 
     * @param string $key_name  约束名
     * @param string $key_type  约束类型
     * @param object $fields    SimpleXMLElement 对象
     * 
     * @return string 一个约束的sql语句字符串
     */
    private function _set_one_key($key_name, $key_type, $fields)
    {
        $array_fields = $array_len = array();
        foreach ($fields->field as $field_info) {
            $array_fields[] = $this->_get_attributes($field_info->attributes(), 'name');
            $array_len[] = $this->_get_attributes($field_info->attributes(), 'length');
        }

        $key_type = strtoupper($key_type);
        switch ($key_type) {
            case 'UNIQUE':
                $create_str = "  UNIQUE KEY `{$key_name}` (`". implode('`,`', $array_fields). "`),\n";
                break;

            case 'INDEX':
                $num_fields = count($array_fields);
                $array_fields_str = array();
                for ($i = 0; $i < $num_fields; $i++) {
                    if (isset($array_len[$i])) {
                        $array_fields_str[] = "`{$array_fields[$i]}`({$array_len[$i]})";
                    } else {
                        $array_fields_str[] = "`{$array_fields[$i]}`";
                    }
                }
                $create_str = "  KEY `{$key_name}` (". implode(',', $array_fields_str). "),\n";
                break;

            case 'PRIMARY':
                $create_str = "  PRIMARY KEY (`". implode('`,`', $array_fields). "`),\n";
                break;

            default:
                $create_str = '';
                break;
        }
        return $create_str;
    }

    // }}}
    // {{{ private function _set_multiple_key

    /**
     * 设置多个约束
     * 
     * @param object $keys  simpxml对象
     * 
     * @return 多个约束的完成sql语句的字符串
     */
    private function _set_multiple_key($keys)
    {
        if ($keys) {
            $create_str = '';
            foreach ($keys->key as $key_field) {
                $fields   = $key_field->fields;
                $key_type = $key_field->type;
                $key_name = $this->_get_attributes($key_field->attributes(), 'name');
                $create_str .= $this->_set_one_key($key_name, $key_type, $fields);
            }
        }

        return $create_str;
    }

    // }}}
    // {{{ private function _create_one_table 

    /**
     * 创建一个表的sql语句
     * 
     * @param array  $columns    列的数组 
     * @param array  $keys       表的约束
     * 
     * @return string 创建一个表的完整的sql语句字符串
     */
    private function _create_one_table($columns, $keys=null) 
    {
        $falg = 'EM_TPL_CREATION_TABLE';
        $table_columns = $this->_set_multiple_column($columns);
        $table_keys    = $this->_set_multiple_key($keys);

        $table_info = array(
                            'table_name' => $this->__table_name,
                            'columns'    => $table_columns,
                            'keys'       => $table_keys,
                            'table_engine' => $this->__table_engine,
                            'table_charset'=> $this->__table_charset,
                      );

        $create_str = $this->_replace_flag($table_info, $falg);
        $create_str = str_replace(",\n\n) ENGINE", "\n) ENGINE", $create_str);

        return $create_str;
    }

    // }}}
    // {{{ private function _create_mulitple_table

    /**
     * 创建多个表
     * 
     * @return 创建多个表的sql语句字符串
     */
    private function _create_mulitple_table($obj_tables)
    {
        if ($obj_tables) {
            $create_str = '';
            foreach ($obj_tables->table as $table_info) {
                $table_name = $this->_get_attributes($table_info->attributes(), 'name');
                $this->_set_table_engine($table_info->engine);
                $this->_set_table_charset($table_info->charset);
                $this->_set_table_name($table_name);

                $columns = $table_info->columns;
                $tab_keys= $table_info->keys;
                $create_str .= $this->_create_one_table($columns, $tab_keys);
            }
        }
        return $create_str;
    }

    // }}}
    // {{{ private function _create_one_table_desc

    /**
     * 创建一个描述表 
     * 
     * @param string $table_desc    表的描述信息
     * @param object $columns       simpxml object
     * @param object $keys          simpxml object
     *
     * @return string  字符串
     */
    private function _create_one_table_desc($table_desc, $columns, $keys) 
    {
        //$table_desc = '表的描述信息在此处';
        $desc_info = 
            array(
                'table_desc' => $table_desc,
                'table_name' => $this->__table_name,
            );
        $create_str  = $this->_replace_flag($desc_info, 'EM_TPL_DESC_TABLE');
        $create_str .= $this->_set_multiple_column_desc($columns);

        $table_columns = $this->_set_multiple_column($columns);
        $table_keys    = $this->_set_multiple_key($keys);

        $table_info = 
            array(
                'table_name' => $this->__table_name,
                'columns'    => $table_columns,
                'keys'       => $table_keys,
                'table_engine' => $this->__table_engine,
                'table_charset'=> $this->__table_charset,
            );
        $create_str .= $this->_replace_flag($table_info, 'EM_TPL_TABLE');
        $create_str  = $create_str = str_replace(",\n\n) ENGINE", "\n) ENGINE", $create_str);

        return $create_str;
    }

    // }}}
    // {{{ private function _create_mulitple_desc_table

    /**
     * 创建多个表的描述 
     * 
     * @return string  创建多个表的描述及sql语句字符串
     */
    private function _create_mulitple_desc_table($obj_tables) 
    {
        if ($obj_tables) {
            foreach ($obj_tables->table as $table_info) {
                $table_name = $this->_get_attributes($table_info->attributes(), 'name');
                $multisplit = $table_info->multisplit;
                if ($multisplit == "true") {
                    $table_name = $this->_split_table_num($table_name);
                }
                $this->_set_table_engine($table_info->engine);
                $this->_set_table_charset($table_info->charset);
                $this->_set_table_name($table_name);

                $columns = $table_info->columns;
                $tab_keys= $table_info->keys;
                $tab_desc= $table_info->desc;
                $create_str .= $this->_create_one_table_desc($tab_desc, $columns, $tab_keys);
            }
        }
        return $create_str;
    }

    // }}}
    // {{{ private function _split_table_num

    /**
     * _split_table_num  分表的情况，把后面的数字去掉 
     * 
     * 
     * @return void
     */
    private function _split_table_num($table_name=null)
    {
        if ($table_name === null) {
            $table_name = $this->__table_name;
        }
        if (preg_match("/^[a-zA-Z_]{1,}_[0-9]{1,}$/", $table_name)) {
            $array_table = explode("_", $table_name);
            $table_num   = array_pop($array_table);
            $split_name  = implode('_', $array_table);
        }
        return $split_name;
    }

    // }}}

    // {{{ public function auto_create_db_schema

    /**
     * 自动生成db_schema.sql的表文件
     * 
     * @return void
     */
    public function auto_create_db_schema()
    {
        if (!$this->_check_xml_obj()) {
            return false;
        }

        foreach ($this->__obj_xml->database as $db_info) {
            $db_name = $this->_get_attributes($db_info->attributes(), 'name'); 
            $db_charset = $db_info->charset;
            $this->_set_db_name($db_name);
            $this->_set_db_character($db_charset);
            $create_db  = $this->_create_database();
            $create_tab = $this->_create_mulitple_table($db_info->tables);
            $create_sql = $create_db . $create_tab;
            $create_file = $this->__creation_path . EM_CREATION_NAME . $db_name . ".sql";
            $res = $this->_write_file($create_sql, $create_file);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    // }}}
    // {{{ public function auto_create_db_desc

    /**
     * 自动生成表描述的sql 
     * 
     * @return void
     */
    public function auto_create_db_desc()
    {
        if (!$this->_check_xml_obj()) {
            return false;
        }

        foreach ($this->__obj_xml->database as $db_info) {
            $db_name = $this->_get_attributes($db_info->attributes(), 'name');
            $db_charset = $db_info->charset;
            $this->_set_db_name($db_name);
            $this->_set_db_character($db_charset);
            $description_tabs = $this->_create_mulitple_desc_table($db_info->tables);
            $description_sqls = EM_TPL_DESC_TOP . $description_tabs;
            $description_file = $this->__desc_path . EM_DESCRIPTION_NAME . $db_name . ".sql";
            $res = $this->_write_file($description_sqls, $description_file);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    // }}}
    // {{{ public function auto_create_html_file

    /**
     * 自动输出表表述的 html 文件 
     * 
     * @return void
     */
    public function auto_create_html_file()
    {
        if ($this->__is_out_html === false) {
            $this->_set_error_info(self::EID_OUTPUT, "OUTPUTING HTML FILE IS CLOSED!");
            return false;
        }

        if (!$this->_check_xml_obj()) {
            return false;
        }

        // write db_xxx.php
        foreach($this->__obj_xml->database as $db_info) {
            $db_name  = $this->_get_attributes($db_info->attributes(), 'name');
            $array_tables = $this->_get_mulitple_table_out_html($db_info->tables);
            $tpl = $this->_get_tpl();
            $tpl->assign('project_key', $this->__project_key);
            $tpl->assign('db_name', $db_name);
            $tpl->assign('tables', $array_tables);
            $tpl->assign('create_time', date("Y-m-d H:i:s"));
            $out_html = $tpl->fetch("main.php");
            unset($tpl);
            $res = $this->_write_file($out_html, $this->__doc_path . "db_{$db_name}.php");
            if (!$res) {
                return false;
            }
        }

        // write side.php
        $db_schema = $this->_get_db_tables(); 
        $tpl = $this->_get_tpl();
        $tpl->assign('project_key', $this->__project_key);
        $tpl->assign('db_schema', $db_schema);
        $out_side_html = $tpl->fetch("side.php");
        unset($tpl);
        $res = $this->_write_file($out_side_html, $this->__doc_path . 'side.php');
        if (!$res) {
            return false;
        }

        // write index.php
        $db_schema = $this->_get_db_tables(); 
        $tpl = $this->_get_tpl();
        $tpl->assign('project_name', $this->__project_name);
        $tpl->assign('project_key', $this->__project_key);
        $out_side_html = $tpl->fetch("index.php");
        unset($tpl);
        $res = $this->_write_file($out_side_html, $this->__doc_path . 'index.php');
        if (!$res) {
            return false;
        }

        // write top.php
        $db_schema = $this->_get_db_tables(); 
        $tpl = $this->_get_tpl();
        $tpl->assign('project_name', $this->__project_name);
        $tpl->assign('project_key', $this->__project_key);
        $out_side_html = $tpl->fetch("top.php");
        unset($tpl);
        $res = $this->_write_file($out_side_html, $this->__doc_path. 'top.php');
        if (!$res) {
            return false;
        }

        return true;
    }

    // }}}
    // }}} end of functions 
}
// end of class

/*
$xml_file = './schema/db_schema.xml';

//创建对象
$em_db = new em_explain_db_schema($xml_file);
$em_db->set_is_out_html(true);

//设置路径
$out_path =
    array (
        'creation' => './',
        'desc'     => './',
    );

$em_db->set_out_path($out_path);

//生成creation文件
$em_db->auto_create_db_schema();

$em_db->auto_create_db_desc();

$em_db->auto_create_html_file();
*/
