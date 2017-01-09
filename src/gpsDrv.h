#ifndef __GPS_DRV_H
#define __GPS_DRV_H
#include <stdint.h>

typedef struct {
  int str;
  uint32_t len;
} gpsDrvIN_frame;

#define GPSDRV_BUFIN_SZ 7000
#define GPSDRV_BUFOUT_SZ 4000

void gpsParse(uint8_t data);
int32_t gpsDrvIN_read(uint8_t **ptr);
int32_t gpsDrvIN_write(uint8_t data);
void gpsDrv_Setup(void);
int32_t gpsDrvOUT_puts(char *str,char ch);
int32_t gpsDrvOUT_write(uint8_t data);

#endif /* __GPS_DRV_H */
