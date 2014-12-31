/*
 * =====================================================================================
 *
 *       Filename:  a.c
 *
 *    Description:  
 *
 *        Version:  1.0
 *        Created:  11/18/2014 05:45:46 PM
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
#include <string.h>

void val(char *);
void ref(char **);

int main(void)
{
    char *p = "libo";

    printf("=======Pass value\n");
    printf("Before\np = %p, p = %s, *p = %c\n", p, p, *p);
    val(p);
    printf("After\np = %p, p = %s, *p = %c\n", p, p, *p);

    printf("=======Pass addr\n");
    printf("Before\np = %p, p = %s, *p = %c\n", p, p, *p);
    ref(&p);
    printf("After\np = %p, p = %s, *p = %c\n", p, p, *p);

    return 0;
}

void val(char *pp)
{
    printf("Inner-before\npp = %p, pp= %s, *pp = %c\n", pp, pp, *pp);
    pp = "bnn";
    printf("Inner-after\npp = %p, pp= %s, *pp = %c\n", pp, pp, *pp);
}

void ref(char **pp)
{
    printf("Inner-before\npp = %p, *pp= %s, **pp = %c\n", pp, *pp, **pp);
    *pp = "bnn";
    printf("Inner-after\npp = %p, *pp= %s, **pp = %c\n", pp, *pp, **pp);
}
