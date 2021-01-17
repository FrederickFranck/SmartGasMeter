from machine import Pin
import time
bit0 = Pin('P23', mode = Pin.IN)
bit1 = Pin('P22', mode = Pin.IN)
bit2 = Pin('P21', mode = Pin.IN)
bit3 = Pin('P20', mode = Pin.IN)
bit4 = Pin('P19', mode = Pin.IN)
bit5 = Pin('P18', mode = Pin.IN)
bit6 = Pin('P17', mode = Pin.IN)
control = Pin('P16', mode = Pin.IN)

value = 0

if(control() == 1):
    if(bit6() == 1):
        value += 64
    if(bit5() == 1):
        value += 32
    if(bit4() == 1):
        value += 16
    if(bit3() == 1):
        value += 8
    if(bit2() == 1):
        value += 4
    if(bit1() == 1):
        value += 2
    if(bit0() == 1):
        value += 1
    print("value: " + str(value))
