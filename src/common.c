#include "common.h"
#include "settings.h"

void common_Setup(void){
#ifdef  VECT_TAB_RAM
  /* Set the Vector Table base location at 0x20000000 */
  NVIC_SetVectorTable(NVIC_VectTab_RAM, 0x0);
#else  /* VECT_TAB_FLASH  */
  /* Set the Vector Table base location at 0x08000000 */
  NVIC_SetVectorTable(NVIC_VectTab_FLASH, 0x0);
#endif
  /* Configure one bit for preemption priority */
  NVIC_PriorityGroupConfig(NVIC_PriorityGroup_1);
  GPIO_InitTypeDef GPIO_InitStructure;
  RCC_AHB1PeriphClockCmd(LOCK_GPIO_CLK, ENABLE);
  GPIO_InitStructure.GPIO_Pin = LOCK_PIN;
  GPIO_InitStructure.GPIO_Speed =GPIO_Medium_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_OUT;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_NOPULL;
  GPIO_Init(LOCK_PORT, &GPIO_InitStructure);

  RCC_AHB1PeriphClockCmd(SYSTEM_GPIO_CLK, ENABLE);
  GPIO_InitStructure.GPIO_Pin = SYSTEM_PIN;
  GPIO_InitStructure.GPIO_Speed =GPIO_Medium_Speed;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_OUT;
  GPIO_InitStructure.GPIO_OType = GPIO_OType_PP;
  GPIO_InitStructure.GPIO_PuPd = GPIO_PuPd_NOPULL;
  GPIO_Init(SYSTEM_PORT, &GPIO_InitStructure);

	/* Systick Configuration */
  if (SysTick_Config(SystemCoreClock / 5000))
  {
    while(1);
  }
  NVIC_SetPriority(SysTick_IRQn, 0x0);

}


void LOCK_ON(void){
  GPIO_SetBits(LOCK_PORT, LOCK_PIN);
}

void LOCK_OFF(void){
  GPIO_ResetBits(LOCK_PORT, LOCK_PIN);
}

void SYSTEM_ON(void){
  GPIO_SetBits(SYSTEM_PORT, SYSTEM_PIN);
}

void SYSTEM_OFF(void){
  GPIO_ResetBits(SYSTEM_PORT, SYSTEM_PIN);
}


static volatile uint32_t TimingDelay;
/**
  * @brief  Inserts a delay time.
  * @param  nTime: specifies the delay time length, in milliseconds.
  * @retval None
  */
void Delay(volatile uint32_t nTime){
  TimingDelay = nTime;
  while(TimingDelay != 0);
}

/**
  * @brief  Decrements the TimingDelay variable.
  * @param  None
  * @retval None
  */
void TimingDelay_Decrement(void){
  if (TimingDelay != 0)
  {
    TimingDelay--;
  }
}

volatile msgPipe gprsPipe={0,0,0};
void waitforit(int32_t timeout){
  gprsPipe.enabled=1;
  TimingDelay=timeout;
  while(TimingDelay > 0)
  {
    if(gprsPipe.sent == 1)
    {
      TimingDelay=timeout;
      gprsPipe.sent = 0;
    }
  }
  gprsPipe.sent = 0;
  gprsPipe.enabled=0;
}
