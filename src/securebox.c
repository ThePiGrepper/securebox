#include "securebox.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

char currlat[15];
char currlon[15];
typedef struct{
  char pass[21];
  char id[21];
  char slat[20];
  char slon[20];
  int lat;
  int lon;
} mdata_s;
mdata_s mdata={0,0,0,0,0,0};

char currAlerts[20];
void addAlert(uint8_t alert){
  char temp[10];
  sprintf(temp,"%d.",alert);
  strcat(currAlerts,temp);
}

void cleanAlert(void){
  *currAlerts=0;
}

char *getAlert(void){
  if(strcmp(currAlerts,"0")!=0)
  {
    uint32_t len = strlen(currAlerts);
    if(len == 0)
    {
      strcpy(currAlerts,"0");
    }
    else
    {
      currAlerts[len-1]=0;
    }
  }
  return currAlerts;
}

uint8_t wifiSetup(char *str)
{
  char *id=strchr(str,'=')+1;
  char *temp=strchr(id,'&');
  *temp = 0;
  strcpy(mdata.id,id);
  char *lat=strchr(temp+1,'=')+1;
  temp=strchr(lat,'&');
  *temp = 0;
  strcpy(mdata.slat,lat);
  lat=strchr(lat,'.')+1;
  mdata.lat = atoi(lat);
  if(mdata.lat<10000) mdata.lat*=10;
  char *lon=strchr(temp+1,'=')+1;
  temp=strchr(lon,'&');
  *temp = 0;
  strcpy(mdata.slon,lon);
  lon=strchr(lon,'.')+1;
  mdata.lon = atoi(lon);
  if(mdata.lon<10000) mdata.lon*=10;
  char *pass=strchr(temp+1,'=')+1;
  //temp=strchr(pass,'&');
  //*temp = 0;
  strcpy(mdata.pass,pass);
  return 1;
}

uint8_t wifiAuth(char *str)
{
  char *pass=strchr(str,'=')+1;
  if(strlen(pass) == (strlen(mdata.pass)+1) && pass[strlen(pass)-1] == '1')
  { //panic alert
    //send gprs alert for wrong pass
    addAlert(6);
    return 1;
  }
  else if(strcmp(mdata.pass,pass) == 0)
  {
    return 1;
  }
  else
  {
    return 0;
  }
}

//returns current link status
uint32_t wifiLinkCount = 0;
uint8_t wifiLink(char *str)
{
  if(strcmp(str,"remote=0") == 0) //no connection
  {
    wifiLinkCount++;
    switch(wifiLinkCount)
    {
      case 3:
              return 1;
      case 12:
              return 2;
      default:
              return 0;
    }
  }
  else if(strcmp(str,"remote=1") == 0) //connected
  {
    wifiLinkCount = 0;
    return 0;
  }
  else
  {
    return 100; //unknown feed
  }
}

#define COORD_DIST 20
int checkPos(int lat, int lon)
{
  if(lat<10000) lat*=10;
  if(lon<10000) lon*=10;
  if(abs(mdata.lat-lat) < COORD_DIST && abs(mdata.lon-lon)< COORD_DIST)
    return 1;
  else
    return -1;
}

static int iscloseCnt = 0;
#define COORDSETSIZE 10
char outstr[500];
static int coordLen=0;
static int coordCnt=0;
uint8_t gprsSendCoord(char *str){
   const char s[] = ",";
   char *token;
   char currStr[64];
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
  sprintf(currlat,"%s%s.%d",slat,ilat,val1);
  sprintf(currlon,"%s%s.%d",slon,ilon,val2);
  if(coordCnt < (COORDSETSIZE-1))
    sprintf(currStr,"lat%d=%s&long%d=%s&", coordCnt,currlat,coordCnt,currlon);
  else
    sprintf(currStr,"lat%d=%s&long%d=%s", coordCnt,currlat,coordCnt,currlon);
  strcpy(outstr + coordLen, currStr);
  coordLen+=strlen(currStr);
  iscloseCnt+=checkPos(val1,val2);
  if(iscloseCnt<0)
    iscloseCnt = 0;
  else if(iscloseCnt>16)
    iscloseCnt = 16;
  char temp[10];
  sprintf(temp,">%d\n",iscloseCnt);
  gpsDrvOUT_puts(temp,0);
  if(++coordCnt >= COORDSETSIZE)
  {
    coordCnt=0;
    coordLen=0;
    return 1;
  }
  else
  {
    return 0;
  }
  //sprintf(outstr,"time:%s, lon:%s%s.%d,lat:%s%s.%d\n", time,slat,ilat,val1,slon,ilon,val2);
  //gprsDrvOUT_puts(outstr,0);
  //return(0);
}

void HW_setup(void){
  common_Setup();
  rpiDrv_Setup();
  wifiDrv_Setup();
  gprsDrv_Setup();
  gpsDrv_Setup();
  LOCK_ON();
  Delay(2500);
  LOCK_OFF();
}

typedef enum { status_opened,status_locked,status_close } lockStatus;

uint8_t idOK = 0;
uint8_t passOK = 0;
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
  lockStatus prevStatus = status_opened;
  //set to status_opened
  HW_setup();
  //wifiSetStatus(wifi_setup);
  uint8_t *streamPtr;
  //gpsDrvOUT_puts("testing..\n",0);
  while(1)
  {
    prevStatus = currStatus;
    currStatus = nextStatus;
    int32_t event_id = EM_getEvent(&currEvent);
    if(event_id != -1)
    {
      switch(currEvent)
      {
        case wifi_e:
          wifiDrvIN_read(&streamPtr);
          *strchr((char *)streamPtr,'\r')=0;
          uint32_t linkTest = wifiLink((char *)streamPtr);
          if(linkTest == 1 && currStatus != status_opened) //test connection with remote device
          {
            //send gprs alert for temporal disconnection
            addAlert(1);
          }
          else if(linkTest == 2 && currStatus != status_opened)
          {
            //send gprs alert for disconnection timeout
            addAlert(2);
          }
          else if(linkTest == 100) //pass-though
          {
            if(currStatus == status_opened)
            {
              //parse data and store it
              if(wifiSetup((char *)streamPtr))
              {
                //all data is saved and the trip can start
                //gpsDrvOUT_puts((char *)streamPtr,0);
                //gpsDrvOUT_write('\n');
                LOCK_ON();
                nextStatus = status_locked;
                idOK = 0;
                passOK = 0;
                wifiSetStatus(wifi_connect);
              }
            }
            else if(currStatus == status_close)
            {
              //parse pass and unlock if ok
              if(wifiAuth((char *)streamPtr)) //check if pass OK
              {
                passOK = 1;
                //gpsDrvOUT_puts((char *)streamPtr,0);
                //gpsDrvOUT_write('\n');
              }
              else
              {
                //send gprs alert for wrong pass
                addAlert(4);
              }
            }
          }
          break;
        case gprs_e:
          gprsDrvIN_read(&streamPtr);
          break;
        case gps_e:
          gpsDrvIN_read(&streamPtr);
          if(currStatus == status_locked || currStatus == status_close)
          {
            gpsDrvOUT_puts((char *)streamPtr,'\n');
            gpsDrvOUT_write('\n');
            if(strchr((char *)streamPtr,'V') == NULL)
            {
              *strchr((char *)streamPtr,'\r')=0;
              if(gprsSendCoord((char *)streamPtr))
              {
                char toutstr[600];
                sprintf(toutstr,"alertas=%s&%s",getAlert(),outstr);
                cleanAlert();
                gprsDrv_SendData(toutstr,0);
                gpsDrvOUT_puts(toutstr,0);
                gpsDrvOUT_write('\n');
                if(iscloseCnt>10)
                {
                  if(currStatus == status_locked)
                  {
                    nextStatus = status_close;
                    wifiSetStatus(wifi_auth);
                  }
                }
                else
                {
                  if(currStatus == status_close)
                  {
                    nextStatus = status_locked;
                    idOK = 0;
                    passOK = 0;
                    wifiSetStatus(wifi_disabled);
                  }
                }
              }
            }
            if(currStatus == status_close && passOK == 1 && idOK == 1)
            {
              addAlert(7);
              LOCK_OFF();
             // nextStatus = status_opened;
              idOK = 0;
              passOK = 0;
              gpsDrvOUT_puts("the END\n",0);
              //gpsDrvOUT_puts(temp,0);
              //gpsDrvOUT_write('\n');
            }
            char temp[100];
            sprintf(temp,"id:%d,pass:%d\n",idOK,passOK);
            gpsDrvOUT_puts(temp,0);
          }
          break;
        case rpi_e:
          rpiDrvIN_read(&streamPtr);
          gpsDrvOUT_puts((char *)streamPtr,'\n');
          gpsDrvOUT_write('\n');
          *strchr((char *)streamPtr,'\r')=0;
          char *cmd=strchr((char *)streamPtr,':')+1;
          *(cmd-1) = 0;
          char temp[25];
          sprintf(temp,"mid:%s,id:%s\n",mdata.id,cmd);
          gpsDrvOUT_puts(temp,0);
          if(strcmp("dni",(char *)streamPtr) == 0)
          {
            if(strcmp(mdata.id,cmd) == 0)
            {
              if(currStatus == status_close) 
              {
                idOK=1;
              }
              else
              {
                addAlert(5);
              }
            }
            else //send alert
            {
              addAlert(3);
            }
          }
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
