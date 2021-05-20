# xizhi-table
send json array and get table markdown in xizhi notification app

``?type=table&content={"A": 1, "B": 2, "C": 3}``

| A | 1 |
| - | - |
| B | 2 |
| C | 3 |

``?type=table&content=[[1,2,3],[4,5,6],[7,8,9]]``

| 1 | 2 | 3 |
| - | - | - |
| 4 | 5 | 6 |
| 7 | 8 | 9 |

``?type=table&content=[{"A":1,"B":11},{"A":2,"B":12},{"A":3,"B":13}]``

| A | B  |
| - | -  |
| 1 | 11 |
| 2 | 12 |
| 3 | 13 |
