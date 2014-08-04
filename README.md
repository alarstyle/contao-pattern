# Pattern

Extension for Contao Open Source CMS. It allows you to add external templates with dynamic variables declared. This templates can be added as the **content elements** or **modules**.

----------

## Usage
Pattern templates must be placed in `template` folder, and all template names should start with `ptr_` prefix (for example `ptr_my_template.html5`).
  
Here is an example:
```
{{%
    "label":
    {
        "en": "Example",
        "ru": "Пример"
    },
    "variables":
    {
        "Variable 1": "text",
        "var_2":
        {
            "label": ["Variable 2", "Just a variable"],
            "type": "textarea",
            "mandatory": true
        },
        "Check me": "checkbox",
        "Image": "image"
    }
%}}
<div>
    {%Variable 1%}
    <?php echo $this->patternVar("var_2") ?>
    <img src="{%Image%}">
</div>
```

## Template labels


One label for all languages:
```
{{%
    "label": "Template Label"
%}}
```
Multilanguage label:
```
{{%
    "label": 
    {
        "en": "Template Label",
        "ru": "Метка Шаблона"
    }
%}}
```
## Variable declaration
There are several methods to define variable.  

Simple one:
```
{{%
    "variables":
    {
        "Var 1": "text"
    }
%}}
```
    
More complex:
```
{{%
    "variables":
    {
        "var_1":
        {
            "label":        ["Var 1", "Some text here"]
            "type":         "text",
            "mandatory":    true,
            "class":        "w50"
        }
    }
%}}
```

## Variable Parameters

**label** - label of variable
You can use any of the following methods, depending on what you prefer
```
"label": "Title"
```
```
"label": ["Title", "Tip"]
```
```
"label": 
{
    "en": "Title",
    "ru": "Заголовок"
}
```
```
"label": 
{
    "en": ["Title, "Tip"],
    "ru": ["Заголовок", "Подсказка"]
}
```
**type** - type of variable ([see types of variables]())
```
"type": "text"
```
**mandatory** - mark variable as mandatory
```
"mandatory": true
```
**class** - specify CSS class or several classes
```
"class": "w50 clr"
```

**extensions** - specify allowed extensions for `file` type.
```
"extensions": "doc, txt"
```

## Variable Types

 - **text**
 - **textarea**
 - **html**
 - **image**
 - **checkbox**
 - **file**
 - **folder**
 - **date**
 - **time**
 - **datetime**
 - **color**

## Variable Output
You can output variable with insert tag 
```
{%var_name%}
``` 
or use PHP function in the template
```
<?php echo $this->patternVar("var_name") ?>
```

----------

# License
[MIT](LICENSE)
Copyright &copy; Alexander Stulnikov


  [1]: docs/sreen_pattern.png "Pattern features preview"