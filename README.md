# Pattern

Extension for Contao Open Source CMS. It allows you to add external templates with dynamic variables declared. This templates can be added as the **content elements** or **modules**.

----------

## Usage
Pattern templates must be placed in `template` folder, and all template names should start with `ptr_` prefix (for example `ptr_my_template.html5`).
  
Here is an example of content:
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

You can define label for template name, it will be shown instead of file name in the template select field.
It can be one label for all backend languages:
```
{{%
    "label": "Template Label"
%}}
```
or specific label for different languages:
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
There are several ways to define variable.  

simple one:
```
{{%
    "variables":
    {
        "Var 1": "text"
    }
%}}
```
    
and more complex:
```
{{%
    "variables":
    {
        "var_1":
        {
            "label":        ["Var 1", "Some tip here"]
            "type":         "text",
            "mandatory":    true,
            "class":        "w50"
        }
    }
%}}
```

## Variable Parameters

**label** - label of variable  
You can use any of the following ways, depending on what you prefer
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
**type** - type of variable ([see types of variables](#types))
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

<a name="types"/>
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
or use PHP function in the template to get the value
```
<?php echo $this->patternVar("var_name") ?>
```

----------

# License
[MIT](LICENSE)  
Copyright &copy; Alexander Stulnikov
