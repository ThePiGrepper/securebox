#ifndef __GRPS_DRV_H
#define __GRPS_DRV_H
#include <stdint.h>

typedef struct {
  int str;
  uint32_t len;
} gprsDrvIN_frame;

typedef enum { DISABLED, ENABLED } gprsStatus;
#define GPRSDRV_BUFIN_SZ 1500
#define GPRSDRV_BUFOUT_SZ 500

void gprsParse(uint8_t data);
int32_t gprsDrvIN_read(uint8_t **ptr);
int32_t gprsDrvIN_write(uint8_t data);
void gprsDrv_SendData(const char *pkg, unsigned char type);
void gprsDrv_Setup(void);
int32_t gprsSetStatus(gprsStatus status);
void gprsReboot(void);
gprsStatus gprsGetStatus(void);
int32_t gprsDrvOUT_write(uint8_t data);
int32_t gprsDrvOUT_puts(char *str,char ch);

#endif /* __GRPS_DRV_H */
