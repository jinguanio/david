/*
 * =====================================================================================
 *
 *       Filename:  a.c
 *
 *    Description:  David
 *
 *        Version:  1.0
 *        Created:  2014年07月15日 14时18分57秒
 *       Revision:  none
 *       Compiler:  gcc
 *
 *         Author:  Dr. Fritz Mehner (mn), mehner@fh-swf.de
 *        Company:  FH Südwestfalen, Iserlohn
 *
 * =====================================================================================
 */


#include	<stdio.h>

#define EXIT_SUCCESS 0
#define e(msg) printf("%s\n\n",   \
        msg);


int
main ( int argc, char *argv[] )
{
    int x = 1, y = 2;
    int *addr = 0;

    printf("%d\t%d\t%p\n", x, y, addr);

    addr = &x;
    *addr += 5;
    printf("%d\t%d\t%p\n", x, y, addr);

    y = *addr;
    printf("%d\t%d\t%p\n", x, y, addr);

    return EXIT_SUCCESS;
}				/* ----------  end of function main  ---------- */

