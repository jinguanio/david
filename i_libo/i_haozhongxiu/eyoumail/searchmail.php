<?php
searchmail phpd模块

// {{{ work 端代码说明

1. work 对外提供的函数 
	a. searchmail_del_index (主动调用) 删除 searchmail 索引
	b. searchmail_create_index_sync_  <server_id> (主动调用) 同步创建索引
	c. searchmail_update_folder_sync_ <server_id>  (主动调用) 同步更新文件夹变更的索引
	d. searchmail_del_index_sync_ <server_id> (主动调用) 同步删除索引
	e. searchmail_find (主动调用) 搜索邮件
	f. on_async_after_receive_mail_searchmail 当接收到邮件触发
	g. on_async_after_draft_mail_searchmail 当删除邮件后触发
	j. on_async_after_change_folder_searchmail 当变更了目录会触发
// }}}

// {{{ fields 字段处理

 const ACCT_ID = 0; // 用户 ID
 const MAIL_ID = 1; // 邮件 ID
 const SUBJECT = 2; // 标题
 const CONTENT = 3; // 正文
 const FROM    = 4; // 发件人
 const TO      = 5; // 收件人
 const ATTNAME = 6; // 附件名称
 const ATTTYPE = 7; // 附件类型
 const FOLDER_ID = 8; // 信件时间
 const MAILTIME= 9; // 信件时间
 const MID     = 10; // 信件唯一ID
 const ALL     = 11; // 查询所有字段

 maps

 self::ACCT_ID   => 'acct_id',
 self::MAIL_ID   => 'mail_id',
 self::SUBJECT   => 'subject',
 self::CONTENT   => 'content',
 self::FROM      => 'mail_from',
 self::TO        => 'mail_to',
 self::ATTNAME   => 'attach_name',
 self::ATTTYPE   => 'attach_type',
 self::FOLDER_ID => 'folder_id',
 self::MAILTIME  => 'mailtime',
 self::MID       => 'mid',
 self::ALL       => 'all',

1. 对所有字段编码 encode 是通过 \0xla 作为分隔符将每个数组元素连接成字符串
2. 对所有字段解码 decode 是通过 \0xla 作为分隔符将其变成数组


// }}}

// {{{ words 分词处理

1. 获取所有分词
2. 获取分词拼装的 sql 表达式
3. 获取所有搜索关键字

// }}}
