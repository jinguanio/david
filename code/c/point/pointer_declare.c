/*
 * =====================================================================================
 *
 *       Filename:  a.c
 *
 *    Description:  
 *
 *        Version:  1.0
 *        Created:  07/24/2014 07:17:24 PM
 *       Revision:  none
 *       Compiler:  gcc
 *
 *         Author:  Dr. Fritz Mehner (mn), mehner@fh-swf.de
 *        Company:  FH Südwestfalen, Iserlohn
 *
 * =====================================================================================
 */

#include <stdio.h>
#include <ctype.h>
#include <stdlib.h>

int func1(float f,int a) { return f * a; }
int func2(float f,int a) { return f + a; }

int
main(int argc, char *argv[]) /* lower: convert input to lower case*/
{
    int (*pFunction)(float,int)=NULL;
    int res;

    pFunction = func1;
    res = pFunction(10.0, 2);
    printf("%s: %d\n", "multiply: ", res);

    pFunction = &func2;
    res = pFunction(10.0, 2);
    printf("%s: %d\n", "add: ", res);
    
    exit(0);
}

