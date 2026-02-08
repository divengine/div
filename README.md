# Div PHP Template Engine

[![Latest Stable Version](https://poser.pugx.org/divengine/div/v)](https://packagist.org/packages/divengine/div)
[![Total Downloads](https://poser.pugx.org/divengine/div/downloads)](https://packagist.org/packages/divengine/div)
[![Latest Unstable Version](https://poser.pugx.org/divengine/div/v/unstable)](https://packagist.org/packages/divengine/div)
[![License](https://poser.pugx.org/divengine/div/license)](https://packagist.org/packages/divengine/div)
[![PHP Version Require](https://poser.pugx.org/divengine/div/require/php)](https://packagist.org/packages/divengine/div)

**div** is a [template engine](https://en.wikipedia.org/wiki/Template_processor) and [code generator](https://en.wikipedia.org/wiki/Code_generation_%28compiler%29) written in [PHP](http://php.net/) and developed since 2011. It optimizes collaboration between developers and designers through generative programming, model-driven architecture, and meta-programming. 

This engine facilitates separation of concerns and allows deep customization by creating tailored template [dialects](https://dialector.divengine.org) to fit specific project needs. Dialects can adapt syntax and behavior to match different frameworks or coding conventions.

A distinctive feature of **div** is its ability to **recursively process templates until all code is resolved**, effectively preventing infinite loops and enabling complex, multi-step transformations. This provides exceptional flexibility to dynamically generate content or code.

**div** is the cornerstone of [Divengine Software Solutions](https://divengine.com) and follows the philosophy *"build more with less"* and *"divide the problem, not the people."* Its code generation relies on clear rules: the model describes what must be done, the templates define the desired output, and the engine acts as a black box executor.

Basic operations:
- **Compile**: Combine a template with models and save the result.
- **Transform**: Convert one model to another, reusing compile.
- **Compose**: Integrate different results using the engine and other tools.

With **div**, teams can:
- Avoid repetitive tasks.
- Scale projects based on models.
- Migrate to different technologies.
- Expand applications to new platforms and devices.

It improves performance and empowers non-technical collaborators to contribute to development.

## Install

```bash
composer require divengine/div
```
## Documentation

For complete guides, usage examples, and advanced topics, please visit the [project Wiki](../../wiki).

If you find something missing or have improvements, feel free to contribute directly to the Wiki!

Powered by [Divengine Software Solutions](https://divengine.com)

