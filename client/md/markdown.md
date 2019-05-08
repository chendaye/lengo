# Markdown
<!-- TOC -->

- [Markdown](#markdown)
    - [标题](#标题)
    - [强调](#强调)
    - [列表](#列表)
    - [添加图片](#添加图片)
    - [添加连接](#添加连接)
    - [引用](#引用)
    - [分割线](#分割线)
    - [code](#code)
    - [任务列表](#任务列表)
    - [表格](#表格)
    - [表情](#表情)
    - [上下标](#上下标)
    - [脚注](#脚注)
    - [标记](#标记)

<!-- /TOC -->
## 标题

- 基本用法

```markdown
# 这是 <h1> 一级标题
## 这是 <h2> 二级标题
### 这是 <h3> 三级标题
#### 这是 <h4> 四级标题
##### 这是 <h5> 五级标题
###### 这是 <h6> 六级标题
```

- 给标题加 id class

```markdown
# 这个标题拥有 1 个 id {#my_id}
# 这个标题有 2 个 classes {.class1 .class2}
```

## 强调

- 斜体

```markdown
*这会是 斜体 的文字*
_这会是 斜体 的文字_
```

- 粗体

```markdown
**这会是 粗体 的文字**
__这会是 粗体 的文字__
```

- 组合

```markdown
_你也 **组合** 这些符号_
```

- 删除横线

```markdown
~~这个文字将会被横线删除~~
```

## 列表

- 无序列表

```markdown
* Item 1
* Item 2
  * Item 2a
  * Item 2b
```

- 有序列表

```markdown
* Item 1
* Item 2
  * Item 2a
  * Item 2b
```

## 添加图片

```markdown
![GitHub Logo](/images/logo.png)
Format: ![Alt Text](url)
```

## 添加连接

```markdown
http://github.com - 自动生成！
[GitHub](http://github.com)
```

## 引用

> 一级引用
>> 二级引用
>>> 三级引用

## 分割线

```markdown
连字符 ---

星号 ***

下划线 ___
```

## code

- 行内code

`echo 'hollo world!'`

- 块内code

```ruby {.class1 .class}
require 'redcarpet'
markdown = Redcarpet.new("Hello World!")
puts markdown.to_html
```

- 块内代码显示行数

```javascript {.line-numbers}
function add(x, y) {
  return x + y
}
```

## 任务列表

- [x] @mentions, #refs, [links](), **formatting**, and <del>tags</del> supported
- [x] list syntax required (any unordered or ordered list supported)
- [x] this is a complete item
- [ ] this is an incomplete item

## 表格

First Header | Second Header | 陈
------------: | :------------- | :----------:
Content from cell 1 | Content from cell 2 | chendaye
Content in the first column | Content in the second column | 666

```markdown
靠右 '--:' 
靠左 ':--' 
居中':--:' 
```

## 表情

:smile:
:fa-car:

## 上下标

- 上标 30^th^
- 下标 H~2~O

## 脚注

```markdown
Content [^1]

[^1]: Hi! This is a footnote
```

## 标记

==marked==

