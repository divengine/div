Div is developed with the philosophy of reused knowledge. Of course, Div is released at the time of the well-known template engines that are widely used. For this reason, Div develops a minimum of new knowledge so that developers can quickly become familiar with this engine and understand when, how and why to use it.

Features are added if really needed. That is, if there is a need to add other functionality, we first analyze whether there is a mechanism to resolve this functionality, and then publish an article explaining how to implement this mechanism.

The argument for developing Div was obtained from several tests with PHP and we concluded that it is faster to replace the parts of the string than scripts included in PHP.

The fact is that substring replacement is a fast process but requires more memory. However, this consumption is so small that it is worth the sacrifice.

The development of div is to avoid creating a caching system because we believe that it is unnecessary based on its characteristics as an engine. A learning system may be enough: it can avoid repeated processing of the same templates.

Finally, the most popular engines are known to be composed of more than one file, classes, and libraries. Div sought from its inception, the implementation of everything in a single class, in a single file. This allows easy adaptation to existing development platforms.