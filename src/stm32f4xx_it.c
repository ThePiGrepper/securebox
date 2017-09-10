/**
  ******************************************************************************
  * @file    GPIO/GPIO_IOToggle/stm32f4xx_it.c
  * @author  MCD Application Team
  * @version V1.5.0
  * @date    06-March-2015
  * @brief   Main Interrupt Service Routines.
  *          This file provides template for all exceptions handler and
  *          peripherals interrupt service routine.
  ******************************************************************************
  * @attention
  *
  * <h2><center>&copy; COPYRIGHT 2015 STMicroelectronics</center></h2>
  *
  * Licensed under MCD-ST Liberty SW License Agreement V2, (the "License");
  * You may not use this file except in compliance with the License.
  * You may obtain a copy of the License at:
  *
  *        http://www.st.com/software_license_agreement_liberty_v2
  *
  * Unless required by applicable law or agreed to in writing, software
  * distributed under the License is distributed on an "AS IS" BASIS,
  * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  * See the License for the specific language governing permissions and
  * limitations under the License.
  *
  ******************************************************************************
  */

/* Includes ------------------------------------------------------------------*/
#include "stm32f4xx_it.h"
#include "securebox.h"
#include "settings.h"
#include "simple_queue.h"


/** @addtogroup STM32F4xx_StdPeriph_Examples
  * @{
  */

/** @addtogroup GPIO_IOToggle
  * @{
  */

/* Private typedef -----------------------------------------------------------*/
/* Private define ------------------------------------------------------------*/
/* Private macro -------------------------------------------------------------*/
/* Private variables ---------------------------------------------------------*/
/* Private function prototypes -----------------------------------------------*/
/* Private functions ---------------------------------------------------------*/

/******************************************************************************/
/*            Cortex-M4 Processor Exceptions Handlers                         */
/******************************************************************************/

/**
  * @brief   This function handles NMI exception.
  * @param  None
  * @retval None
  */
void NMI_Handler(void)
{
}

/**
  * @brief  This function handles Hard Fault exception.
  * @param  None
  * @retval None
  */
void HardFault_Handler(void)
{
  /* Go to infinite loop when Hard Fault exception occurs */
  while (1)
  {
  }
}

/**
  * @brief  This function handles Memory Manage exception.
  * @param  None
  * @retval None
  */
void MemManage_Handler(void)
{
  /* Go to infinite loop when Memory Manage exception occurs */
  while (1)
  {
  }
}

/**
  * @brief  This function handles Bus Fault exception.
  * @param  None
  * @retval None
  */
void BusFault_Handler(void)
{
  /* Go to infinite loop when Bus Fault exception occurs */
  while (1)
  {
  }
}

/**
  * @brief  This function handles Usage Fault exception.
  * @param  None
  * @retval None
  */
void UsageFault_Handler(void)
{
  /* Go to infinite loop when Usage Fault exception occurs */
  while (1)
  {
  }
}

/**
  * @brief  This function handles SVCall exception.
  * @param  None
  * @retval None
  */
void SVC_Handler(void)
{
}

/**
  * @brief  This function handles Debug Monitor exception.
  * @param  None
  * @retval None
  */
void DebugMon_Handler(void)
{
}

/**
  * @brief  This function handles PendSVC exception.
  * @param  None
  * @retval None
  */
void PendSV_Handler(void)
{
}

/**
  * @brief  This function handles SysTick Handler.
  * @param  None
  * @retval None
  */
void SysTick_Handler(void)
{
  TimingDelay_Decrement();
}

/******************************************************************************/
/*                 STM32F4xx Peripherals Interrupt Handlers                   */
/*  Add here the Interrupt Handler for the used peripheral(s) (PPP), for the  */
/*  available peripheral interrupt handler's name please refer to the startup */
/*  file (startup_stm32f40xx.s/startup_stm32f427x.s/startup_stm32f429x.s).    */
/******************************************************************************/

/**
  * @brief  This function handles PPP interrupt request.
  * @param  None
  * @retval None
  */
/*void PPP_IRQHandler(void)
{
}*/

/**
  * @}
  */

/**
  * @}
  */
void WIFI_IRQHandler(void)
{
  if(USART_GetITStatus(WIFI_MODULE, WIFI_RX_IT))
  {
    uint8_t rxdata = (uint8_t) USART_ReceiveData(WIFI_MODULE);
//    gprsDrvOUT_write(rxdata);
    wifiParse(rxdata);
  }
}

void GPS_IRQHandler(void)
{
  if(USART_GetITStatus(GPS_MODULE, GPS_RX_IT))
  {
    uint8_t rxdata = (uint8_t) USART_ReceiveData(GPS_MODULE);
    //gprsDrvOUT_write(rxdata);
    gpsParse(rxdata);
  }
}

extern volatile msgPipe gprsPipe;
void GPRS_IRQHandler(void)
{
  if(USART_GetITStatus(GPRS_MODULE, GPRS_RX_IT))
  {
    uint8_t rxdata = (uint8_t) USART_ReceiveData(GPRS_MODULE);
    if(gprsPipe.enabled == 1)
    {
      gprsPipe.sent = 1;
    }
//    gprsDrvOUT_write(rxdata);
    //gprsParse(rxdata);
    //LOCK_OFF();
    //gprsDrvOUT_write("*");
    //USART_ClearITPendingBit(USART2,USART_IT_RXNE);
  }
}

void RPI_IRQHandler(void)
{
  if (SPI_I2S_GetITStatus(RPI_MODULE, RPI_RX_IT) == SET)
  {
      uint8_t rxdata = SPI_I2S_ReceiveData(RPI_MODULE);
      rpiParse(rxdata);
      //rpiDrvIN_write(rxdata);
      //SPI_I2S_ITConfig(SPIx, SPI_I2S_IT_RXNE, DISABLE);
  }
}

void ACCGYRO_IRQHandler(void)
{
  uint8_t rxdata = 0;
  switch(I2C_GetLastEvent(ACCGYRO_MODULE))
  {
    case I2C_EVENT_SLAVE_RECEIVER_ADDRESS_MATCHED :
      break;
    case I2C_EVENT_SLAVE_BYTE_RECEIVED:
      rxdata = I2C_ReceiveData(ACCGYRO_MODULE); // Store the packet in i2c_read_packet.
      accgyroParse(rxdata);
      break;
    case I2C_EVENT_SLAVE_STOP_DETECTED:
      //if(I2C_GetFlagStatus(ACCGYRO_MODULE,I2C_FLAG_ADDR) == SET)
      //   I2C_ClearFlag(ACCGYRO_MODULE,I2C_FLAG_ADDR);
      //if(I2C_GetFlagStatus(ACCGYRO_MODULE,I2C_FLAG_STOPF) == SET)
      //   I2C_ClearFlag(ACCGYRO_MODULE,I2C_FLAG_STOPF);
      break;
    //case I2C_EVENT_SLAVE_TRANSMITTER_ADDRESS_MATCHED:
    //  I2C_SendData(ACCGYRO_MODULE, i2c_packet_to_send[0]);
    //  Tx_Index++;
    //  break;
    //case I2C_EVENT_SLAVE_BYTE_TRANSMITTED:
    //  I2C_SendData(ACCGYRO_MODULE, i2c_packet_to_send[Tx_Index]);
    //  Tx_Index++;
    //  break;
    default:
      break;
  }
}
/************************ (C) COPYRIGHT STMicroelectronics *****END OF FILE****/
