#ifndef __WIFI_DRV_H
#define __WIFI_DRV_H
#include <stdint.h>

typedef struct {
  uint8_t *str;
  uint32_t len;
} wifiDrvIN_frame;

#define WIFIDRV_BUFIN_SZ 400
#define WIFIDRV_BUFOUT_SZ 400

#define WIFI_RST_ON GPIO_SetBits(WIFI_PORT2, WIFI_RST_PIN)
#define WIFI_RST_OFF GPIO_ResetBits(WIFI_PORT2, WIFI_RST_PIN)
#define WIFI_MODE_PROG GPIO_SetBits(WIFI_PORT2, WIFI_MODE_PIN)
#define WIFI_MODE_USER GPIO_ResetBits(WIFI_PORT2, WIFI_MODE_PIN)

typedef enum {
  wifi_disabled,
  wifi_setup,
  wifi_auth
} wifiStatus;

void wifiParse(uint8_t data);
int32_t wifiDrvIN_read(wifiDrvIN_frame **addr);
int32_t wifiDrvIN_write(uint8_t data);
void wifiDrv_Setup(void);
int32_t wifiSetStatus(wifiStatus status);
wifiStatus wifiGetStatus(void); 
int32_t wifiDrvOUT_write(uint8_t data);
int32_t wifiDrvOUT_puts(char *str);

#endif /* __WIFI_DRV_H */
