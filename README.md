# Trabajo Practico Especial: WEB 2 2023

### Integrantes trabajo:
    - Lopez de Armentia, Santiago.
      - DNI: 37.014.604
      - Email: santiagolopezdearmentia2@gmail.com
      
    - Gigena, Maximiliano.
      - DNI: 33.356.983
      - Email: gigenamaximiliano2@gmail.com

### Temática:
  Ecommerce de productos gaming.

### Breve descripción:
  Venta de productos/componentes gaming como por ejemplo: perifericos en general (teclados, mouse, etc), consolas de video juegos, componentes para el armado de pc´s, notebooks, etc.
  
  Se contara con las siguientes tablas **[productos, fabricantes]**.

### Diagrama Entidad-Relacion


  ![Diagrama Entidad-Relacion](diagrama-entidad-relacion.jpg)


# Documentación API

## GET


1. URL: api/productos
  Devuelve todos los productos que se encuentren en la base de datos.

  Ejemplo response:
  ```json
    [
      {
        "id_producto": 34,
        "nombre": "Protector pantalla",
        "descripcion": "Aumenta la vida util del monitor.",
        "id_fabricante": 3,
        "ruta_imagen": "img_productos/default.png",
        "precio": 43650,
        "moneda": "ARG",
        "fabricante": "Gigabyte"
      }
    ]
  ```

A su vez, este puede utilizarse con diferentes cadenas de consulta [Filtrado(filter), Ordenamiento(sort), Paginación(pagination)].

### Filtrado (filter)
  Filtrado se implementa utilizando los siguientes parámetros: api/productos?filter=[valor]&filterValue=[valor].

  | Parámetro | Tipo | Ejemplo | Descripción |
  |----------|----------|----------|----------|
  | filter    | String   | filter=nombre   | Valor nombre de la columna. Se realizara el filtro por la columna [nombre] de la tabla.|
  | filterValue    | String   | filterValue=Pad   | Valor de búsqueda. Se aplicara el filtro en la columna [nombre] por el valor [Pad]. |



### Ordenamiento (sort)
  Ordenamiento se implementa utilizando los siguientes parámetros: api/productos?sort=[valor]&order=[valor].

  | Parámetro | Tipo | Ejemplo | Descripción |
  |----------|----------|----------|----------|
  | sort    | String   | sort=nombre   | Valor nombre de la columna. Se realizara el ordenamientopor la columna [nombre] de la tabla.|
  | order    | String   | order=desc   | Valor de búsqueda. Se aplicara el ordenamiento en orden descendente. Posibles valores admitidos [asc/desc]. |

### Paginación (pagination)
  Paginación se implementa utilizando los siguientes parámetros: api/productos?page=[valor]&limit=[valor]

  | Parámetro | Tipo | Ejemplo | Descripción |
  |----------|----------|----------|----------|
  | page    | String   | page=2   | Número de página a mostrar.|
  | limit    | String   | limit=3   | Límite de elementos que se deberán mostrar. |

### Valores por defecto

  Cuando se hace uso de la cadena de parametro principal filter, sort, page y estos no toman ningun valor o no se encuentra el parámetro compañero, se toman los siguientes valores por defecto. Lo mismo aplica para los parametros secundarios.

  | Parámetro | Tipo | Ejemplo | Valor por defecto |
  |----------|----------|----------|----------|
  | filter    | String   | page   | nombre |
  | filterValue    | String   | page   |  |
  | sort    | String   | sort   | id_producto |
  | order    | String   | sort   | asc |
  | page    | String   | page  | 1 |
  | limit    | String   | page  | 3 |

La combinación de todos los parámetros se encuentra disponible. Ejemplo: api/productos?filter=[valor]&filterValue=[valor]&sort=[valor]&oder=[valor]&page=[valor]&limit=[valor].

2. URL: api/productos/:ID

  Devuelve un producto que se encuentren en la base de datos por el ID del mismo.

  Ejemplo request: 
  * api/productos/17

  Ejemplo response:
  ```json
   {
      "id_producto": 17,
      "nombre": "Protector pantalla",
      "descripcion": "Aumenta la vida util del monitor.",
      "id_fabricante": 3,
      "ruta_imagen": "img_productos/default.png",
      "precio": 43650,
      "moneda": "ARG",
      "fabricante": "Gigabyte"
    }
  ```

Adicional, se encuentra disponible la posibilidad de solicitar un *subrecurso* del producto.
  URL: api/productos/:ID/:subrecurso

  Ejemplo request:
  * api/productos/17/descripcion

  Ejemplo response:
  ```json
   Aumenta la vida util del monitor.
  ```


## POST

  Ingresa un nuevo producto en el sistema.
  1. URL: api/productos

  Body de la request:
  ```json
   {
      "nombre": "Protector pantalla",
      "descripcion": "Aumenta la vida util del monitor.",
      "id_fabricante": 3,
      "precio": 43650,
      "moneda": "ARG"
    }
  ```
  >[!PREREQUISITES]
  >
  > **Los siguientes son los campos requeridos para la creacion de un producto: nombre, descripcion, id_fabricante, precio, moneda.**

  >[!NOTE]
  >
  > **La imagen no es requerida, por lo que, en caso de no asignarse una, se pondra una por defecto.**
  > **Para agregar una imagen se requiere usar el tag "ruta_imagen" y se debera colocar la ruta completa.**

  >[!NOTE]
  >
  > **Los productos contiene un campo de "oferta" el cual no es requerido al momento de su creación. en caso de no asignarse, se dara un valor por defecto. El campo es de tipo booleano ya admite los valores [0/1].**

  > ```json
  > {
  >   "nombre": "Protector pantalla",
  >   "descripcion": "Aumenta la vida util del monitor.",
  >   "id_fabricante": 3,
  >   "ruta_imagen": "C:\\Documentos\\Imagenes\\producto.png",
  >   "precio": 43650,
  >   "moneda": "ARG",
  >   "oferta": 1
  > }
  >```
  

  >[!IMPORTANT]
  >
  > **Verbo POST requiere autenticacion.**

## PUT

Actualiza un producto dado mediante ID. Se sigue la misma metodologia que para la creacion de un producto.

  1. URL: api/productos/39

  Body de la request:
  ```json
   {
      "nombre": "Protector pantalla",
      "descripcion": "Aumenta la vida util del monitor.",
      "id_fabricante": 3,
      "precio": 43650,
      "moneda": "ARG"
    }
  ```
   >[!PREREQUISITES]
  >
  > **Los siguientes son los campos requeridos para la creacion de un producto: nombre, descripcion, id_fabricante, precio, moneda.**

  >[!NOTE]
  >
  > **La imagen no es requerida, por lo que, en caso de no asignarse una, se pondra una por defecto.**
  > **Para agregar una imagen se requiere usar el tag "ruta_imagen" y se debera colocar la ruta completa.**

  >[!NOTE]
  >
  > **Los productos contiene un campo de "oferta" el cual no es requerido al momento de su creación. en caso de no asignarse, se dara un valor por defecto. El campo es de tipo booleano ya admite los valores [0/1].**

  > ```json
  > {
  >   "nombre": "Protector pantalla",
  >   "descripcion": "Aumenta la vida util del monitor.",
  >   "id_fabricante": 3,
  >   "ruta_imagen": "C:\\Documentos\\Imagenes\\producto.png",
  >   "precio": 43650,
  >   "moneda": "ARG",
  >   "oferta": 1
  > }
  >```
  

  >[!IMPORTANT]
  >
  > **Verbo PUT requiere autenticacion.**


  **AUTORIZACION**

**GET** 
URL: : api/user/token

Devuelve, de ser correctos los datos introducidos (usuario y contraseña),  un token que permite autenticarse.

Los datos requeridos para la generacion del mismo se deberan completar en los campos que ofrece Authorization, Type: Basic Auth. 

Ejemplo response:

 "edfgyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF91c3VhcmlvIjoxLCJ1c3VhcmlvIjoid2ViYWRtaW4iLCJjb250cmFzZW5pYSI6IiQyeSQxMCRmV0NwbWE3UXYFLdUxOOFZCUVpPVGR5LmZIY25dcL1pEZjh2TEgyNUdOaWtDOEM3TEMiLCJleHAiOjE2OTk5MTU1ODZ9.xld7LJM-B_xJYHeVUMA51864ID5O1IehjkudYt2KJY2BpY"

    
    
   
El token generado mediante este endpoint será requerido para todos los request de tipo POST y PUT de las entidades de datos. Deberá agregarse a los Headers del request en el siguiente formato:

  Key: Authorization Value:  Bearer **Token**    



