#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>
#define NUM_THREADS 10
/* print tread id and then exit */
void *PrintHello(void *tid)!
{
   printf("Hello world from thread #%d!\n", (long) tid);
   pthread_exit(0);
}
/* create 10 threads and then exit */
int main (int argc, char *argv[])
{
  pthread_t tid[NUM_THREADS];
  int err; long t;
  for (t = 0; t < NUM_THREADS; t++){
    printf("In main: creating thread %ld\n", t);
      if (err = pthread_create(&tid[t], NULL, PrintHello, (void *)t)) {
         printf("ERROR; return code from pthread_create() is %d\n", err);
         exit(EXIT_FAILURE);
      }
  } 
}