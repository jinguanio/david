#!/bin/bash
#===============================================================================
#
#          FILE:  check_gd.sh
# 
#         USAGE:  ./check_gd.sh 
# 
#   DESCRIPTION:  检查 GD 库函数
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  10/27/2010 10:04:05 AM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   配置参数
#-------------------------------------------------------------------------------

# {{{ CHECK_PATH

CHECK_PATH=(
    "/home/libo/link/dev/src/web/php"
    "/home/libo/link/dev/src/lib"
)

# }}}

# {{{ GD_FUNC_LIST

GD_FUNC_LIST=(
	"gd_info"
	"getimagesize"
	"image_type_to_extension"
	"image_type_to_mime_type"
	"image2wbmp"
	"imagealphablending"
	"imageantialias"
	"imagearc"
	"imagechar"
	"imagecharup"
	"imagecolorallocate"
	"imagecolorallocatealpha"
	"imagecolorat"
	"imagecolorclosest"
	"imagecolorclosestalpha"
	"imagecolorclosesthwb"
	"imagecolordeallocate"
	"imagecolorexact"
	"imagecolorexactalpha"
	"imagecolormatch"
	"imagecolorresolve"
	"imagecolorresolvealpha"
	"imagecolorset"
	"imagecolorsforindex"
	"imagecolorstotal"
	"imagecolortransparent"
	"imageconvolution"
	"imagecopy"
	"imagecopymerge"
	"imagecopymergegray"
	"imagecopyresampled"
	"imagecopyresized"
	"imagecreate"
	"imagecreatefromgd2"
	"imagecreatefromgd2part"
	"imagecreatefromgd"
	"imagecreatefromgif"
	"imagecreatefromjpeg"
	"imagecreatefrompng"
	"imagecreatefromstring"
	"imagecreatefromwbmp"
	"imagecreatefromxbm"
	"imagecreatefromxpm"
	"imagecreatetruecolor"
	"imagedashedline"
	"imagedestroy"
	"imageellipse"
	"imagefill"
	"imagefilledarc"
	"imagefilledellipse"
	"imagefilledpolygon"
	"imagefilledrectangle"
	"imagefilltoborder"
	"imagefilter"
	"imagefontheight"
	"imagefontwidth"
	"imageftbbox"
	"imagefttext"
	"imagegammacorrect"
	"imagegd2"
	"imagegd"
	"imagegif"
	"imagegrabscreen"
	"imagegrabwindow"
	"imageinterlace"
	"imageistruecolor"
	"imagejpeg"
	"imagelayereffect"
	"imageline"
	"imageloadfont"
	"imagepalettecopy"
	"imagepng"
	"imagepolygon"
	"imagepsbbox"
	"imagepsencodefont"
	"imagepsextendfont"
	"imagepsfreefont"
	"imagepsloadfont"
	"imagepsslantfont"
	"imagepstext"
	"imagerectangle"
	"imagerotate"
	"imagesavealpha"
	"imagesetbrush"
	"imagesetpixel"
	"imagesetstyle"
	"imagesetthickness"
	"imagesettile"
	"imagestring"
	"imagestringup"
	"imagesx"
	"imagesy"
	"imagetruecolortopalette"
	"imagettfbbox"
	"imagettftext"
	"imagetypes"
	"imagewbmp"
	"imagexbm"
	"iptcembed"
	"iptcparse"
	"jpeg2wbmp"
	"png2wbmp"
)

# }}}

LOG="`pwd`/res.log"

#-------------------------------------------------------------------------------
#   逻辑   
#-------------------------------------------------------------------------------

for path_item in ${CHECK_PATH[@]}; do
    cd $path_item
    echo -e "Directory: $path_item, waiting for ..."

    file_num=0
    start_time=`date +%s`

    for php in `find . -name "*.php"`; do
        echo $php
        let "file_num += 1"

        for gd_func in ${GD_FUNC_LIST[@]}; do
            cat $php | grep $gd_func > /dev/null

            if [ "0" -eq "$?" ]; then
                echo $php >> $LOG
                break
            fi
        done
    done

    end_time=`date +%s`
    let "run_time = $end_time - $start_time"

    echo -e "Total: $file_num, Run Time: $run_time S.\n"
done
