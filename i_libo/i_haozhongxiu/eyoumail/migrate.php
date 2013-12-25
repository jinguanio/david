<?php
通过 gearman 来调度迁移过程

// {{{ gearman work

1. phpd 中运行，client 端回调回的 job 对象中传递到 work 需要迁移用户的 acct_id ,和迁移唯一标识符
2. 通过 acct_id 构造用户对象
3. 通过用户对象创建迁移配置对象（主要功能是获取此用户的迁移相关参数）
4. 通过获取出的迁移配置的协议、用户对象、迁移唯一标识符创建邮件迁移的对象 ***
5. 设置迁移日志、设置迁移中的进程的计数器

6. 执行迁移 <USER_MIGRATE>


// }}}
// {{{ USER_MIGRATE :用户邮件迁移的主要逻辑
// {{{ main

1. 初始化迁移结果数据表的数据,就是在 user_migrate_result 表中添加一条迁移数据，本表每次迁移都会添加一条，不论是否已经同一个用户并且迁移唯一标识符一样。本质上就是控制异步的本次迁移的状态的。
2. 验证迁移，具体的验证内容：
	a. 判断是否在 user_migrate 表中存在 acct_id 和迁移唯一标识符的记录，（本记录在执行 em_migrate 迁移工具的 init 命令后产生）
	b. 判断是否正在有其他的进程执行本次迁移
	c. 如果以上的验证成功了则将该记录置为正在处理的状态
	d. 验证配置是否正确（迁移唯一标识符）

	NB: 在验证这个过程中需要把 user_migrate 表加锁，具体的方式是通过事务的形式，进去先执行个空的修改动作
3. 执行具体的迁移逻辑，具体方式有两种分别是 pop3,imap
	<POP3>
	<IMAP>

4. 迁移完毕将用户的成员配置 migrate_set 、 migrate_auth 分别置为 0

// }}}
// {{{ POP3 : pop3 方式

1. 初始化邮件目录
	a. 判断是否在迁移配置文件中配置了 mig_popf (迁移的目标文件夹)
	b. 如果没有配置返回系统默认的收件箱的ID,如果存在在数据库中查询返回文件夹 ID
	c. 如果迁移配置的文件夹在系统中不存在则创建一个文件夹后返回文件夹 ID
2. 设置本次迁移的开始状态, 更新 user_migrate_result 中的 start_time （开始时间）置为系统当前的时间 、status （执行状态）置为 1

3. 获取需要迁移的 uidl (先通过 pop3 获取出全部，然后和已经迁移成功的比较排除)
	a. 获取最后一次 UIDL 数据 , 从  user_migrate_result 表中通过按完成时间排序来获取
	b. 通过迁移配置文件中的 mig_mid 配置项来确定获取查找旧的 uidl 的方式
		I. 当配置成 1 => index 时是从 user_mail_index_XX 中获取，根据 migrate_name 、migrate_unique 来获取，这两个字段在正常存储信件的时候为空，只有迁移进去的邮件才会有值
		II. 当配置成 2 => result 时是从 filed 中获取旧的 uidl, 因为所有的 迁移过程在前已完成后都会把这些 uidl 写入 field中。包括目录的信息。
	c. 将旧的 uidl 排除掉，并且做出相关的统计，并更新到数据库中 。
	d. 循环所有的 uidl 进行信件的迁移
		I. 将 pop3 retr 资源句柄、uidl 以及目录  ID 传递到 em_migrate_base.class.php 中的 _migrate_mail 方法中进行统一的信件保存工作。<MAILSAVE>

// }}}
// {{{ IMAP : imap 方式

1. 设置本次迁移的开始状态, 更新 user_migrate_result 中的 start_time （开始时间）置为系统当前的时间 、status （执行状态）置为 1

2. 初始化 imap 对象
3. 初始化邮件目录
	a. 从 imap 服务器上获取当前用户的文件夹， 并且有配置获取 mig_imapsf_* （系统默认文件夹）、mig_imapf_custom （自定义文件夹）、mig_imapf_ignore （忽略文件夹） 
	b. 通过配置中获取的文件夹比较做出相应的操作，如果在配置中的文件夹在系统中不存在则会创建一个

4. 获取需要迁移的 uidl (先通过 imap 获取出全部，然后和已经迁移成功的比较排除)
	a. 获取最后一次 UIDL 数据 , 从  user_migrate_result 表中通过按完成时间排序来获取
	b. 通过迁移配置文件中的 mig_mid 配置项来确定获取查找旧的 uidl 的方式
		I. 当配置成 1 => index 时是从 user_mail_index_XX 中获取，根据 migrate_name 、migrate_unique 来获取，这两个字段在正常存储信件的时候为空，只有迁移进去的邮件才会有值
		II. 当配置成 2 => result 时是从 filed 中获取旧的 uidl, 因为所有的 迁移过程在前已完成后都会把这些 uidl 写入 field中。包括目录的信息。
	c. 将旧的 uidl 排除掉，并且做出相关的统计，并更新到数据库中 。
	d. 循环所有的 uidl 进行信件的迁移
		邮件存储 <MAILSAVE>

// }}}
// {{{ MAILSAVE : 保存迁移的邮件 (资源句柄, uidl, 目录ID)

// }}}

// }}}
