Each template can have properties and the designer is responsible for establishing them. The properties apply only to the file where they are defined.

The properties are defined in a single line using the following syntax:

```html
@_property_name = property's value
```

For example, the following code sets the dialect of the template, where the value is the name of the file containing the dialect.

```html
@_DIALECT = smarty.dialect
```

Properties are identified by the prefix **@\_.**
