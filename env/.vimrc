"显示当前行号
set ru

"自动切换到当前目录
set autochdir

"用4个空格代替tab
set sts=4

"智能缩进
set cinoptions=:0,p0,t0
set cinw=if,else,while,do,for,switch,case
set formatoptions=tqo
set cin

"高亮
syn on

"搜索高亮
set hls
set incsearch

"在插入 tab 的时候用一定数量的空格数代替 tab
set et

"制表符的显示的空格个数
set ts=4

"控制 > 和 < 的移动空格数为4个
set sw=4

"在调用 :n 或 :N 命令时自动保存已经修改的文件。
set aw

"设置代码折叠
set foldmethod=marker

"命令显示
set showcmd

"设置gf命令搜索路径
"set path+=/usr/local/eyou/mail/app/lib/php,/usr/local/eyou/mail/web/php/user

"设置状态栏显示信息
"set statusline=%F%m%r%h%w\[FORMAT=%{&ff}]\[TYPE=%Y]\[ASCII=\%03.3b]\[HEX=\%02.2B]\[POS=%04l,%04v][%p%%]\[LEN=%L]
set statusline=%F%m%r%h%w\[LINE=%04l]\[COL=%04v]\[LEN=%L]
set laststatus=2
"
" Set to auto read when a file is changed from the outside
set autoread

" Sets how many lines of history VIM har to remember
set history=400

" 设置编码
set fenc=utf-8
set encoding=utf-8
set fileencodings=utf-8,gbk,cp936,latin-1

" ======= 引号 && 括号自动匹配 ======= "

:inoremap ( ()<ESC>i

:inoremap ) <c-r>=ClosePair(')')<CR>

:inoremap { {}<ESC>i

:inoremap } <c-r>=ClosePair('}')<CR>

:inoremap [ []<ESC>i

:inoremap ] <c-r>=ClosePair(']')<CR>

:inoremap " ""<ESC>i

:inoremap ' ''<ESC>i

:inoremap ` ``<ESC>i

function ClosePair(char)
  if getline('.')[col('.') - 1] == a:char
     return "\<Right>"
  else
     return a:char
  endif
endf
