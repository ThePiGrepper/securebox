#include "securebox.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

char outstr[100];
void gprsSendCoord(char *str){
   const char s[] = ",";
   char *token;
   char time[15];
   char ilat[15];
   char *dlat=NULL;
   char ilon[15];
   char *dlon=NULL;
   char slat[2]={0,0};
   char slon[2]={0,0};
   int val1;
   int val2;
   unsigned char i=0;
   char *ptr;
   /* get the first token */
   token = strtok(str, s);
   /* walk through other tokens */
   while( token != NULL )
   {
      switch(i)
      {
        case 0: //time
               strcpy(time,token);
               time[6]=0;
               break;
        case 2:
               strcpy(ilat,token);
               ptr=strchr(ilat,'.');
               *ptr=*(ptr-1);
               *(ptr-1)=*(ptr-2);
               *(ptr-2)=0;
               dlat=ptr-1;
               break;
        case 3:
               if('S'==token[0])
                 slat[0]='-';
               break;
        case 4:
               strcpy(ilon,token);
               ptr=strchr(ilon,'.');
               *ptr=*(ptr-1);
               *(ptr-1)=*(ptr-2);
               *(ptr-2)=0;
               dlon=ptr-1;
               break;
        case 5:
               if('W'==token[0])
                 slon[0]='-';
               break;
      }
      i++;
     token = strtok(NULL, s);
  }
  val1=strtol(dlat,NULL,10)/60;
  val2=strtol(dlon,NULL,10)/60;
  sprintf(outstr,"lat=%s%s.%d&long=%s%s.%d", slat,ilat,val1,slon,ilon,val2);
  //sprintf(outstr,"time:%s, lon:%s%s.%d,lat:%s%s.%d\n", time,slat,ilat,val1,slon,ilon,val2);
  //gprsDrvOUT_puts(outstr,0);
  //return(0);
}

void HW_setup(void){
  common_Setup();
  rpiDrv_Setup();
  //wifiDrv_Setup();
  gprsDrv_Setup();
  gpsDrv_Setup();
  LOCK_ON();
  Delay(2500);
  LOCK_OFF();
}

typedef enum { status_opened,status_locked } lockStatus;

void proto_main(void){
  //stages: status_opened, status_locked.
  //status_opened->status_locked: needs: setup=1.
  //  LOCK_ON
  //  starts reading gps
  //  starts receiving syncro data
  //  start sending gprs each T seconds.
  //status_locked->status_opened:
  //  gps_match()=1 then auth_on //needs global var for wifi_state: {disabled,setup,auth}
  //  gps_match()=0 when disable_wifi()
  event_type currEvent;
  lockStatus currStatus = status_opened;
  lockStatus nextStatus = status_opened;
  //set to status_opened
  HW_setup();
  //wifiSetStatus(wifi_setup);
  //gprsDrvOUT_puts("this is a test",0);
  uint8_t *streamPtr;
  //gprsDrvOUT_puts("testing..",0);
  while(1)
  {
    char *ptr;
    int32_t event_id = EM_getEvent(&currEvent);
    if(event_id != -1)
    {
      switch(currEvent)
      {
        case wifi_e:
          wifiDrvIN_read(&streamPtr);
          gprsDrvOUT_write('+');
          gprsDrvOUT_puts(streamPtr,'\n');
          gprsDrvOUT_write('\n');
          //wifiDrvOUT_puts(streamPtr,'\n');
          //wifiDrvOUT_write('\n');
          break;
        case gprs_e:
          gprsDrvIN_read(&streamPtr);
          //LOCK_OFF();
          //wifiDrvOUT_puts(streamPtr,'\n');
          //wifiDrvOUT_write('\n');
          break;
        case gps_e:
          gpsDrvIN_read(&streamPtr);
          gpsDrvOUT_puts(streamPtr,'\n');
          gpsDrvOUT_write('\n');
          if(strchr(streamPtr,'V') == NULL)
          {
            *strchr(streamPtr,'\r')=0;
            gprsSendCoord(streamPtr);
            gprsDrv_SendData(outstr);
            gpsDrvOUT_puts(outstr,0);
            gpsDrvOUT_write('\n');
            //gprsDrvOUT_puts(streamPtr,'\n');
            //gprsDrvOUT_write('\n');
          }
          break;
        case rpi_e:
          rpiDrvIN_read(&streamPtr);
          gpsDrvOUT_puts(streamPtr,'\n');
          gpsDrvOUT_write('\n');
          break;
        default:
          gpsDrvOUT_write('+');
          gpsDrvOUT_puts("oops\n",0);
      }
    }
  }
  //status_opened
  while(1)
  {
    currStatus = nextStatus;
    int32_t event_id = EM_getEvent(&currEvent);
    switch(currStatus)
    {
      case status_opened:
        //reset status
        if(event_id != -1)
        {
          switch(currEvent)
          {
            case wifi_e:
              //handle setup info
              //if setup info is ok, go to status_locked
              //do lock init setup HERE
              wifiSetStatus(wifi_disabled);
              nextStatus = status_locked;
              break;
            case gps_e:
              //discard data
              break;
          }
        }
        break;
      case status_locked:
        if(event_id != -1)
        {
          switch(currEvent)
          {
            case wifi_e:
              //discard data and disable
              break;
            case gps_e:
              //receive string and extract time and coordinates
              //save a value set each 10 samples and send to gprs
              //if they match to destination coords for 10 samples it enables wifi_auth.
              //if at least 10 samples dont match, it goes back to wifi_disabled.

              break;
          }
        }
        break;
      default:
        nextStatus = status_locked;
    }
	}
}
