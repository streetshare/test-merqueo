# Tecnología

Para el desarrollo de la prueba se utilizó el lenguaje de programación PHP 8, bajo el framework Codeigniter 4, y su patrón MVC. Conexión a base de datos MySQL. Implementación de Api Rest.

# Requisitos

Antes de realizar la ejecución del proyecto se deben configurar las variables de conexión que 
se encuentran en el archivo ".env", ubicado en la raiz del proyecto. Se deben descomentar las
variables correspondientes a "database.default" y ajustar con los datos de conexión que
correspondan con su base de datos. 
# Instalación

El proyecto está configurado para correr con el comando "composer load", el cual ejecuta las siguientes sentencias:
    - creación de base de datos
        php spark db:create merqueo
    - Creación de tabla
        php spark migrate
    - Carga inicial de información
        php spark db:seed Init
    - Iniciar el proyecto
        php spark serve

Una vez ejecutado el comando, el proyecto se visualizará en la siguiente url:
    - http://localhost:8080


# Solución
La lógica que se utilizó para llegar a la solución fue tener un arqueo de caja con el listado
de billetes y monedas, junto con la cantidad de cada uno de ellos. Al momento de realizar la
operación de venta se valida si existe suficiente efectivo para dar cambio, si el resultado
es exitoso se procede a guardar la información en la tabla de logs, y actualizar los valores
de la caja y su respectivo arqueo. 

En cuanto a la validación del efectivo, se toma como base
la cantidad a devolver y se compara contra el billete/moneda de mayor denominación que se 
encuentra por debajo de valor mencionado, y se calcula cuantos billetes se necesitan para la
operación; A continuación se resta del valor total, la cantidad conseguida con los billetes,
si dicho valor es diferente de cero, se ejecuta de manera recurrente el mismo procedimiento
hasta lograr el cometido. Puede darse que se complete satisfactoriamente el proceso, como puede
que no sea así, en cuyo caso se envia un mensaje al usuario, notificando el error.

Para un ejemplo práctico, supongamos que nos pagan una transacción de $ 34.500, con un billete
de $ 50.000, lo que genera un cambio de $ 15.500, para lo cual se procede así:
    - Se valida el billete de mayor denominación que esta por debajo del valor a devolver,
      en este caso $10.000
    - Es requerido un billete de $10.000, si existe en la caja se guarda la información en un
      arreglo y se procede a consultar nuevamente la función, descontando los $10.000
    - En el siguiente ciclo el valor a devolver es $4.500, igualmente se busca el billete de
      mayor denominacion que está bajo el valor, es decir $2.000
    - Realizando los ciclos correspondientes tendremos que operación optima para realizar la
      devolución sería así:
        * 1 billete de 10.000
        * 2 billetes de 2.000
        * 1 moneda de 500
    - Todo lo anterior en el caso que la caja cuente con los billetes/monedas necesarios, de lo
      contrario el sistema generará otra combinación buscando resolver la operación; si no se
      puede resolver la operación se notificará al usuario el faltante de efectivo.

Finalmente cabe aclarar, que el listado de billetes/monedas se implementó con un código que
permite diferenciar cada uno de los mismos y sus correspondientes cantidades.

# Estructura

El proyecto se compone de un controlador principal llamado Api, por donde se dará acceso a los
servicios, en la sección de modelos se definieron 3 modelos que interactuan directamente con la
base de datos (BoxModel, CashModel, LogsModel) y 1 modelo que se encarga de orquestar las 
operaciones. Todos tienen un formato de respuesta estandarizado para una mayor lectura al 
momento de consultar los servicios.
# Pruebas

Para la realización de las pruebas se puede ejecutar de la siguiente forma:
    - importando la colección de request existente en Postman, bajo la url
        https://www.getpostman.com/collections/6b574f0c74d096783ec9
        
## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.