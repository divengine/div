To make calculations and other advantages. The formula is a valid PHP expression. See the [[Appendix A - Allowed PHP functions]]

**Syntax in templates**  
    

```html
(# formula #)
    
OR
    
(# formula : number format #)
```

     
The number format are explained in Data formats.

**Example**

index.tpl  
    

```html
{= number: 200.000 =}
{=  price:  20.000 =}
{=    tax:   0.345 =}
    
5 + {$number} = (# 5 + {$number} #)
    
Price with tax: ${$price} + ${#tax:2.#} = $(# {$price} + {$tax} :2. #)
```

      
Output

```html
5 + 200 = 205
    
Price with tax: $20 + $0.35 = $20.35
```

