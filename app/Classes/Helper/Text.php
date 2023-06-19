<?php

namespace App\Classes\Helper;

class Text{

    CONST ADD_SUCCESS         = "Registro exitoso.";
    CONST COLUMN_ADDRESS      = "address";
    CONST COLUMN_EXTRA        = "extra";
    CONST COLUMN_PARTNER      = "partner";
    CONST MED_COMERCIAL       = "medidas_comerciales";
    CONST INICIAL             = "cuota_inicial";
    CONST COLUMN_ACCOUNT      = "account";
    CONST RESPONSE            = "response";
    CONST RESPONSE_TEXT       = "responseText";
    CONST MAIL_FROM           = "From => ";
    CONST MAIL_REPLY          = "Reply-To => ";
    CONST MAIL_HEADERS        = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    CONST LINE_LINE           = "\r\n";
    CONST MAIL_CC             = "Cc:";
    CONST DISPLAY_ERROR       = "display_errors";
    CONST AUTHORIZATION       = "Authorization";
    CONST TOKEN_DECLINE       = "TOKEN decline.";
    CONST ACCESS_DECLINE      = "Access decline.";
    CONST COLUMN_ID           = "id";
    CONST COLUMN_NAME         = "name";
    CONST COLUMN_ID_CITY      = "id_city";
    CONST COLUMN_TOKEN        = "token";
    CONST COLUMN_DOMAIN       = "domain";
    CONST COLUMN_CREATED      = "created_at";
    CONST COLUMN_UPDATED      = "updated_at";
    CONST LONGITUD            = "longitud";
    CONST LONGITUDE           = "longitude";
    CONST LATITUD             = "latitud";
    CONST LATITUDE            = "latitude";
    CONST ADDRESS_EXTRA       = "address_extra";
    CONST ID_MUNICIPALITY     = "id_municipality";
    CONST ID_COUNTRY          = "id_country";
    CONST IDADDRESS_EXTRA     = "id_address_extra";
    CONST ID_LOCALIZATION     = "id_localization";
    CONST LOCALHOST           = "127.0.0.1";
    CONST IP_HOST             = "http://ipinfo.io/";
    CONST BARRA_JSON          = "/json";
    CONST CERO                = 0;
    CONST COMA                = ",";
    CONST ROUNDS              = "rounds";
    CONST PARTNER_ALREADY     = "Partner already registered.";
    CONST NIT                 = "nit";
    CONST EMAIL               = "email";
    CONST RAZON_SOCIAL        = "razon_social";
    CONST L_REPRESENTA        = "legal_representative";
    CONST USERNAME            = "username";
    CONST PASSWORD            = "password";
    CONST EMAIL_ALREADY       = "Email already registered.";
    CONST CITY                = "city";
    CONST COUNTRY             = "country";
    CONST MUNICIPALITY        = "municipality";
    CONST INTEGRATION         = "integrations_api";
    CONST CODE                = "code";
    CONST ID_ROL              = "id_rol";
    CONST ROL                 = "rol";
    CONST ID_PERMISSIONS      = "id_permissions";
    CONST ARROBA              = "@";
    CONST SPACE               = " ";
    CONST MESSAGES_LOGN       = [
        "Bienvenido.",
        "Contraseña erronea.",
        "La cuenta se encuentra desactivada.",
        "El usuario no se encuentra registrado.",
        "El partner ingresado no existe.",
        "Formato invalido de usuario.",
        "La cuenta no existe."
    ];
    CONST ENCRYP_METHOD       = "sha256";
    CONST ENCRYP_KEY          = "ENCRYPTION_KEY";
    CONST ID_ACCOUNT          = "id_account";
    CONST STATUS              = "status";
    CONST STORE               = "store";
    CONST SHEETS              = "sheets";
    CONST WAREHOUSES          = "warehouses";
    CONST Enable              = "Cuenta habilitada.";
    CONST Disable             = "Cuenta deshabilitada.";
    CONST TYPE                = "type";
    CONST KEY                 = "key";
    CONST VALUE               = "value";
    CONST Partner_None        = "La cuenta no esta asignada a un partner.";
    CONST ID_CATALOG          = "id_catalog";
    CONST ID_PARTNER          = "id_partner";
    CONST PRODUCT             = "product";
    CONST PRODUCTS            = "products";
    CONST PRODUCTOS           = "productos";
    CONST STORES              = "stores";
    CONST ID_STORE            = "id_store";
    CONST STORE_NAME          = "store_name";
    CONST ID_BRAND            = "id_brand";
    CONST BRAND               = "brand";
    CONST ID_CLACOM           = "id_clacom";
    CONST ID_TYPE             = "id_type";
    CONST SKU                 = "sku";
    CONST SKU_API             = "Sku";
    CONST QUERY               = "query";
    CONST LIKE                = "like";
    CONST CATEGORIAS          = "categorias";
    CONST ACCOUNT_STATUS      = "accountStatus";
    CONST ROL_ACCOUNT         = "rolAccount";
    CONST NEGATIVE_ID         = "-1";
    CONST DISTINCT_SYMBOL     = "!=";
    CONST PERCENT             = "%";
    CONST IMPORT_SUCCESS      = "Importacion exitosa.";
    CONST OBJECT              = "object";
    CONST ERROR_PARAMETRO     = "Error de parametros.";
    CONST SEARCH_ERROR        = "Busqueda erronea.";
    CONST SEARCH_SUCCESS      = "Busqueda exitosa.";
    CONST QUERY_SUCCESS       = "Datos obtenidos exitosamente.";
    CONST ACCOUNT_REGIS       = "La cuenta ya se encuentra registrada.";
    CONST CATALOG_EXIST       = "El catalogo ya existe.";
    CONST CATALOG_NOEXIST     = "El catalogo no existe.";
    CONST NONE_FILTER         = "El filtro seleccionado no existe.";
    CONST ACCOUNT_NOT         = "La cuenta consultada no existe.";
    CONST ACCOUNT_YES         = "Cuenta obtenida.";
    CONST NULL_TYPE           = null;
    CONST CATEGORY_NONE       = "La categoria no existe.";
    CONST GRUPO_ARTICULO      = "GrupoArticulo";
    CONST DISPONIBILIDAD      = "Disponibilidad";
    CONST PRECIOS             = "Precios";
    CONST PRECIOS_POS         = "precios";
    CONST SUB_CATEGORIA       = "SubCategoria";
    CONST METHOD_POST         = "POST";
    CONST METHOD_GET          = "GET";
    CONST POS_PARAM_ONE       = "Content-Type:application/json; charset=utf-8";
    CONST POS_AUTH            = "Authorization: Basic ";
    CONST ORDER_DESC          = "DESC";
    CONST ORDER_ASC           = "ASC";
    CONST CODIGO              = "codigo";
    CONST MARCA               = "marca";
    CONST NOMBRE              = "nombre";
    CONST DETALLE             = "detalle";
    CONST CLACOM              = "clacom";
    CONST ATTRIBUTES          = "attributes";
    CONST TIPO_PRODUCTO       = "tipoProducto";
    CONST TEXT_NONE           = "";
    CONST MINICUOTAS          = "minicuotas";
    CONST ESTADO              = "estado";
    CONST CLASIFICACION       = "clasificacion";
    CONST VISIBLE             = "visible";
    CONST FILTROS             = "filtros";
    CONST DISPONIBILIDAD_     = "disponibilidad";
    CONST STOCKDISPONIBLE     = "stockDisponible";
    CONST NOMBRE_ALMACEN      = "nombreAlmacen";
    CONST ID_PRODUCT          = "id_product";
    CONST ID_WAREHOUSE        = "id_warehouse";
    CONST STOCK               = "stock";
    CONST BASE                = "base";
    CONST ALMACEN             = "almacen";
    CONST ALMACEN_API         = "Almacen";
    CONST ID_PRICE            = "id_price";
    CONST ALMACEN_CENTRAL     = "almacenCentral";
    CONST PRICE               = "price";
    CONST SPECIAL_PRICE       = "special_price";
    CONST FROM_DATE           = "from_date";
    CONST TO_DATE             = "to_date";
    CONST LISTA_PRECIO        = "listaPrecio";
    CONST PRECIO              = "precio";
    CONST DESCUENTO           = "descuento";
    CONST ADD_ONE_YEAR        = " + 1 years";
    CONST CODIGO_PADRE        = "codigoPadre";
    CONST ID_CATEGORY         = "id_category";
    CONST ID_POS              = "id_pos";
    CONST POS_SUBCATEGORY     = "sub_category_pos";
    CONST NAME_POS            = "name_pos";
    CONST CUOTAS              = "cuotas";
    CONST GUION_BAJO          = "_";
    CONST LABEL               = "label";
    CONST TEXT                = "text";
    CONST CUOTA               = "cuota";
    CONST MONTO               = "monto";
    CONST NO_EXIST_SKU        = "El sku no se encuenta asignado a un producto.";
    CONST SKU_NONE            = "El sku % no existe.";
    CONST ID_NONE             = "El id % no existe.";
    CONST CANTIDAD            = "cantidad";
    CONST CUSTOM              = "custom";
    CONST PRICES              = "prices";
    CONST WAREHOUSE           = "warehouse";
    CONST DESCRIPCION         = "descripcion";
    CONST NO_RESPONSE         = "Sin resultados.";
    CONST URL                 = "url";
    CONST LANDING             = "landing";
    CONST METADATA            = "metadata";
    CONST TITULO              = "titulo";
    CONST INHERITANCE         = "inheritance";
    CONST SUB_CAT_POS         = "sub_category_pos";
    CONST TITLE               = "title";
    CONST BODY                = "body";
    CONST IN_MENU             = "in_menu";
    CONST INFO                = "info";
    CONST UPDATE_SUCCESS      = "Datos actualizados exitosamente.";
    CONST ID_METADATA         = "id_metadata";
    CONST ID_CAT_INFO         = "id_category_info";
    CONST PROFILE             = "profile";
    CONST COVER               = "cover";
    CONST RELATION_WH_P       = [
        "product_warehouse.id_warehouse",
        "product_warehouse",
        "product.id",
        "=",
        "product_warehouse.id_product",
        "product_warehouse.id_warehouse"
    ];
    CONST STORES_ID           = "stores_id";
    CONST SOCIAL_NETWORK      = "id_social_network";
    CONST IMAGE               = "image";
    CONST DAYS_DIFENCENS      = " hace % dias.";
    CONST DAY_DIFENCENS       = " hace % dia.";
    CONST MONTH_DIFENCENS     = " hace % mes.";
    CONST MOTHS_DIFENCENS     = " hace % meses.";
    CONST YEAR_DIFENCENS      = " hace % año.";
    CONST YEARS_DIFENCENS     = " hace % años.";
    CONST CREATED_DIF         = "frecuence_created";
    CONST UPDATED_DIF         = "frecuence_updated";
    CONST DESCRIPTION         = "description";
    CONST TIME                = "time";
    CONST PARTNER_NONE        = "El partner no existe.";
    CONST TOKEN_NONE          = "El token del partner no existe.";
    CONST COUNTRY_NONE        = "No disponemos de covertura en el país solicitado.";
    CONST CITY_NONE           = "No disponemos de covertura en la ciudad solicitada.";
    CONST MUNICIPALITY_NONE   = "No disponemos de covertura en el municipio solicitado.";
    CONST COLUMN_CUSTOMER     = "customer";
    CONST PAIS                = "Pais";
    CONST CIUDAD              = "Ciudad";
    CONST MUNICIPIO           = "Municipio";
    CONST LOCALIZACION        = "Localizacion";
    CONST DIRECCION           = "Direccion";
    CONST DIRECCION_EXTRA     = "DireccionExtra";
    CONST TIPO_DOCUMENTO      = "TipoDocumento";
    CONST NOMBRE_API          = "Nombre";
    CONST APELLIDO_PATERNO_API= "ApellidoPaterno";
    CONST APELLIDO_MATERNO_API= "ApellidoMaterno";
    CONST EMAIL_API           = "Email";
    CONST NUM_TELEFONO_API    = "NumTelefono";
    CONST NUM_DOCUMENTO_API   = "NumDocumento";
    CONST SYMBOL_MAYOR        = ">";
    CONST QTY                 = "Qty";
    CONST TOTAL               = "Total";
    CONST PRECIO_UNITARIO     = "PrecioUnitario";
    CONST ID_PARTNER_API      = "IdPartner";
    CONST COL_APELLIDO_PATERNO= "apellido_paterno";
    CONST COL_APELLIDO_MATERNO= "apellido_materno";
    CONST COL_NUM_TELEFONO    = "num_telefono";
    CONST COL_TIPO_DOCUMENTO  = "tipo_documento";
    CONST COL_NUM_DOCUMENTO   = "num_documento";
    CONST TOTAL_DESCUENTO     = "TotalDescuento";
    CONST DESCUENTOS          = "Descuentos";
    CONST MONTO_API           = "Monto";
    CONST PORCENTAJE_API      = "Porcentaje";
    CONST ERROR_PROFORMA_QTY  = "La cantidad de los productos no coincide con el detalle de la proforma.";
    CONST ERROR_PROFORMA_SUBT = "El subtotal de los productos no coincide con el detalle de la proforma.";
    CONST ERROR_PROFORMA_MONTO= "El monto total de los productos no coincide con el detalle de la proforma.";
    CONST ERROR_PROFORMA_TOTAL= "El total de los descuento no coincide con el detalle de la proforma.";
    CONST ERROR_TIPO_DOCUMENTO= "El tipo de documento no es valido.";
    CONST DATOS_CLIENTES      = "DatosCliente";
    CONST TOKEN_PARTNER       = "TokenPartner";
    CONST SUB_TOTAL           = "SubTotal";
    CONST CANTIDAD_PRODUCTOS  = "CantidadProductos";
    CONST DETALLE_ORDEN       = "DetalleOrden";
    CONST STATUS_SALES_NONE   = "El estado de la orden no existe.";
    CONST NRO_FACTURA         = "NroFactura";
    CONST NRO_PROFORMA        = "NroProforma";
    CONST NRO_CONTROL         = "NroControl";
    CONST COUPON_CODE         = "coupon_code";
    CONST COUPON_NONE         = "El cupon no se encuentra registrado.";
    CONST COLUMN_SALES        = "sales";
    CONST COLUMN_COUPON       = "coupon";
    CONST LIMIT_USAGE_COUPON  = "Se excedio el límite del cupon.";
    CONST CODIGO_DESCUENTO    = "CodigoDescuento";
    CONST COLUMN_NRO_FACTURA  = "nro_factura";
    CONST COLUMN_NRO_PROFORMA = "nro_proforma";
    CONST COLUMN_NRO_CONTROL  = "nro_control";
    CONST EXIST_SALE          = "Lo sentimos no se pueden registrar ordenes duplicadas.";
    CONST ORDEN_SUCCESS       = "La orden fue registrada exitosamente.";
    CONST COUPON_DISABLE      = "El cupon se encuentra desactivado.";
    CONST WAREHOUSE_NONE      = "El almacen seleccionado no existe.";
    CONST WAREHOUSE_PRODUCT   = "El producto no existe en el almacen.";
    CONST STOCK_PRODUCT_NONE  = "El producto cuenta con un stock insuficiente.";
    CONST STORE_MAGENTO       = "StoreMagento";
    CONST STORE_NO_EXIST      = "La tienda seleccionada no existe.";
    CONST FECHA_COMPROMISO    = "FechaCompromiso";
    CONST ORDER_NONE          = "La orden no existe.";
    CONST PRODUCT_NONE        = "El producto no existe.";
    CONST ORDER_CANCELADA     = "La orden se cancelo con exito.";
    CONST ORDER_COMPLETADA    = "La orden se finalizo con exito.";
    CONST ORDER_CERRADA       = "La orden se cerro con exito.";
    CONST PARTNER_ID_API      = "PartnerId";
    CONST FILTERS             = "filters";
    CONST DATE_INI            = "date_ini";
    CONST DATE_END            = "date_end";
    CONST IP                  = "ip";
    CONST DETAIL_ORDER        = "detail_order";
    CONST HISTORY_STATUS      = "history_status";
    CONST DETAIL_DISCOUNT     = "detail_descount";
    CONST CUPON_API           = "Cupon";
    CONST CUPON_CODE_API      = "Codigo_Cupon";
    CONST DETALLE_WAREHOUSE   = "Detalle_Warehouse";
    CONST FILE_UNDEFINED      = "El archivo no se pudo encontrar.";
    CONST DATE_LIMIT          = "date_limit";
    CONST PRECESS_DATA_SUCCESS= "Datos procesados exitosamente.";
    CONST STATUS_COLUMN       = "Status";
    CONST ID_API              = "ID";
    CONST EJECUCION_API       = "Ejecucion";
    CONST DURACION_API        = "Duracion";
    CONST FECHA_EJECUCION_API = "FechaEjecucion";
    CONST FECHA_DURACION_API  = "FechaDuracion";
    CONST PROCESS_NONE        = "El proceso solicitado no existe.";
    CONST MENSAJE             = "mensaje";
    CONST SEGUNDOS_DURACION   = " segundos.";
    CONST CATEGORY            = "category";
    CONST WIDTH               = "width";
    CONST HEIGHT              = "height";
    CONST WEIGHT              = "weight";
    CONST VOLUME              = "volume";
    CONST STRING              = "string";
    CONST INT                 = "int";
    CONST FLOAT               = "float";
    CONST BOOL                = "bool";
    CONST INDEX               = "index";
    CONST DATA                = "data";
    CONST DELIMITER_CODE      = ";";
    CONST ID_DESCRIPTION      = "id_description";
    CONST ID_MED_COMERCIALES  = "id_medidas_comerciales";
    CONST ANCHO               = "ancho";
    CONST ALTURA              = "altura";
    CONST VOLUMEN             = "volumen";
    CONST PESO                = "peso";
    CONST ID_ATTRIBUTE        = "id_attribute";

    public function __construct() {
        //
    }

    /**
     * @return string
     */
    public function getIdAttribute(){
        return SELF::ID_ATTRIBUTE;
    }

    /**
     * @return string
     */
    public function getAncho(){
        return SELF::ANCHO;
    }

    /**
     * @return string
     */
    public function getAltura(){
        return SELF::ALTURA;
    }

    /**
     * @return string
     */
    public function getVolumen(){
        return SELF::VOLUMEN;
    }

    /**
     * @return string
     */
    public function getPeso(){
        return SELF::PESO;
    }

    /**
     * @return string
     */
    public function getIdMedidasComerciales(){
        return SELF::ID_MED_COMERCIALES;
    }

    /**
     * @return string
     */
    public function getIdDescription(){
        return SELF::ID_DESCRIPTION;
    }

    /**
     * @return string
     */
    public function getDelimiterCode(){
        return SELF::DELIMITER_CODE;
    }

    /**
     * @return string
     */
    public function getData(){
        return SELF::DATA;
    }

    /**
     * @return string
     */
    public function getIndex(){
        return SELF::INDEX;
    }

    /**
     * @return string
     */
    public function getFloat(){
        return SELF::FLOAT;
    }

    /**
     * @return string
     */
    public function getBool(){
        return SELF::BOOL;
    }

    /**
     * @return string
     */
    public function getInt(){
        return SELF::INT;
    }

    /**
     * @return string
     */
    public function getString(){
        return SELF::STRING;
    }

    /**
     * @return string
     */
    public function getWidth(){
        return SELF::WIDTH;
    }

    /**
     * @return string
     */
    public function getHeight(){
        return SELF::HEIGHT;
    }

    /**
     * @return string
     */
    public function getWeight(){
        return SELF::WEIGHT;
    }

    /**
     * @return string
     */
    public function getVolume(){
        return SELF::VOLUME;
    }

    /**
     * @return string
     */
    public function getCategory(){
        return SELF::CATEGORY;
    }

    /**
     * @return string
     */
    public function getDuracionProcess(){
        return SELF::SEGUNDOS_DURACION;
    }

    /**
     * @return string
     */
    public function getMensaje(){
        return SELF::MENSAJE;
    }

    /**
     * @return string
     */
    public function getProcessNone(){
        return SELF::PROCESS_NONE;
    }

    /**
     * @return string
     */
    public function getIdApi(){
        return SELF::ID_API;
    }

    /**
     * @return string
     */
    public function getEjecucionApi(){
        return SELF::EJECUCION_API;
    }

    /**
     * @return string
     */
    public function getDuracionApi(){
        return SELF::DURACION_API;
    }

    /**
     * @return string
     */
    public function getFechaEjecucionApi(){
        return SELF::FECHA_EJECUCION_API;
    }

    /**
     * @return string
     */
    public function getFechaDuracionApi(){
        return SELF::FECHA_DURACION_API;
    }

    /**
     * @return string
     */
    public function getStatusColumn(){
        return SELF::STATUS_COLUMN;
    }

    /**
     * @return string
     */
    public function getDataProcessSuccess(){
        return SELF::PRECESS_DATA_SUCCESS;
    }

    /**
     * @return string
     */
    public function getDateLimit(){
        return SELF::DATE_LIMIT;
    }

    /**
     * @return string
     */
    public function getFileUndefined(){
        return SELF::FILE_UNDEFINED;
    }

    /**
     * @return string
     */
    public function getDetalleWarehouse(){
        return SELF::DETALLE_WAREHOUSE;
    }

    /**
     * @return string
     */
    public function getCouponCodeApi(){
        return SELF::CUPON_CODE_API;
    }

    /**
     * @return string
     */
    public function getCuponApi(){
        return SELF::CUPON_API;
    }

    /**
     * @return string
     */
    public function getDetailDiscount(){
        return SELF::DETAIL_DISCOUNT;
    }

    /**
     * @return string
     */
    public function getHistoryStatus(){
        return SELF::HISTORY_STATUS;
    }

    /**
     * @return string
     */
    public function getDetailOrder(){
        return SELF::DETAIL_ORDER;
    }

    /**
     * @return string
     */
    public function getIp(){
        return SELF::IP;
    }

    /**
     * @return string
     */
    public function getDateIni(){
        return SELF::DATE_INI;
    }

    /**
     * @return string
     */
    public function getDateEnd(){
        return SELF::DATE_END;
    }

    /**
     * @return string
     */
    public function getFilters(){
        return SELF::FILTERS;
    }

    /**
     * @return string
     */
    public function getPartnerId(){
        return SELF::PARTNER_ID_API;
    }

    /**
     * @return string
     */
    public function getOrdenCancelada(){
        return SELF::ORDER_CANCELADA;
    }

    /**
     * @return string
     */
    public function getOrdenCompletada(){
        return SELF::ORDER_COMPLETADA;
    }

    /**
     * @return string
     */
    public function getOrdenCerrada(){
        return SELF::ORDER_CERRADA;
    }

    /**
     * @return string
     */
    public function getProductNone(){
        return SELF::PRODUCT_NONE;
    }

    /**
     * @return string
     */
    public function getOrdenNone(){
        return SELF::ORDER_NONE;
    }

    /**
     * @return string
     */
    public function getFechaCompromiso(){
        return SELF::FECHA_COMPROMISO;
    }

    /**
     * @return string
     */
    public function getStoreNone(){
        return SELF::STORE_NO_EXIST;
    }

    /**
     * @return string
     */
    public function getStoreMagento(){
        return SELF::STORE_MAGENTO;
    }

    /**
     * @return string
     */
    public function getStockNoneProduct(){
        return SELF::STOCK_PRODUCT_NONE;
    }

    /**
     * @return string
     */
    public function getWarehouseProductNone(){
        return SELF::WAREHOUSE_PRODUCT;
    }

    /**
     * @return string
     */
    public function getAlmacenApi(){
        return SELF::ALMACEN_API;
    }
    
    /**
     * @return string
     */
    public function getWarehouseNone(){
        return SELF::WAREHOUSE_NONE;
    }

    /**
     * @return string
     */
    public function getCouponDisable(){
        return SELF::COUPON_DISABLE;
    }

    /**
     * @return string
     */
    public function getOrderSuccess(){
        return SELF::ORDEN_SUCCESS;
    }

    /**
     * @return string
     */
    public function getExistSale(){
        return SELF::EXIST_SALE;
    }

    /**
     * @return string
     */
    public function getColumnNroFactura(){
        return SELF::COLUMN_NRO_FACTURA;
    }

    /**
     * @return string
     */
    public function getColumnNroProforma(){
        return SELF::COLUMN_NRO_PROFORMA;
    }

    /**
     * @return string
     */
    public function getColumnNroControl(){
        return SELF::COLUMN_NRO_CONTROL;
    }

    /**
     * @return string
     */
    public function getPorcentajeApi(){
        return SELF::PORCENTAJE_API;
    }

    /**
     * @return string
     */
    public function getCodigoDescuento(){
        return SELF::CODIGO_DESCUENTO;
    }

    /**
     * @return string
     */
    public function getColumnLimitCoupon(){
        return SELF::LIMIT_USAGE_COUPON;
    }

    /**
     * @return string
     */
    public function getColumSale(){
        return SELF::COLUMN_SALES;
    }

    /**
     * @return string
     */
    public function getColumnCoupon(){
        return SELF::COLUMN_COUPON;
    }

    /**
     * @return string
     */
    public function getCouponNone(){
        return SELF::COUPON_NONE;
    }

    /**
     * @return string
     */
    public function getCouponCode(){
        return SELF::COUPON_CODE;
    }

    /**
     * @return string
     */
    public function getNroControl(){
        return SELF::NRO_CONTROL;
    }

    /**
     * @return string
     */
    public function getNroFactura(){
        return SELF::NRO_FACTURA;
    }

    /**
     * @return string
     */
    public function getNroProforma(){
        return SELF::NRO_PROFORMA;
    }

    /**
     * @return string
     */
    public function getStatusSaleNone(){
        return SELF::STATUS_SALES_NONE;
    }

    /**
     * @return string
     */
    public function getTokenPartner(){
        return SELF::TOKEN_PARTNER;
    }

    /**
     * @return string
     */
    public function getSubTotal(){
        return SELF::SUB_TOTAL;
    }

    /**
     * @return string
     */
    public function getCantidadProductos(){
        return SELF::CANTIDAD_PRODUCTOS;
    }

    /**
     * @return string
     */
    public function getDetalleOrden(){
        return SELF::DETALLE_ORDEN;
    }

    /**
     * @return string
     */
    public function getDatosClientes(){
        return SELF::DATOS_CLIENTES;
    }

    /**
     * @return string
     */
    public function getErrorTipoDocumento(){
        return SELF::ERROR_TIPO_DOCUMENTO;
    }

    /**
     * @return string
     */
    public function getErrorSubTotalProforma(){
        return SELF::ERROR_PROFORMA_SUBT;
    }

    /**
     * @return string
     */
    public function getErrorMontoProforma(){
        return SELF::ERROR_PROFORMA_MONTO;
    }

    /**
     * @return string
     */
    public function getErrorTotalProforma(){
        return SELF::ERROR_PROFORMA_TOTAL;
    }

    /**
     * @return string
     */
    public function getErrorQtyProforma(){
        return SELF::ERROR_PROFORMA_QTY;
    }

    /**
     * @return string
     */
    public function getTotalDescuento(){
        return SELF::TOTAL_DESCUENTO;
    }

    /**
     * @return string
     */
    public function getDescuentos(){
        return SELF::DESCUENTOS;
    }

    /**
     * @return string
     */
    public function getMontoApi(){
        return SELF::MONTO_API;
    }

    /**
     * @return string
     */
    public function getColApellidoPaterno(){
        return SELF::COL_APELLIDO_PATERNO;
    }

    /**
     * @return string
     */
    public function getColApellidoMaterno(){
        return SELF::COL_APELLIDO_MATERNO;
    }

    /**
     * @return string
     */
    public function getColNumTelf(){
        return SELF::COL_NUM_TELEFONO;
    }

    /**
     * @return string
     */
    public function getColTipoDoc(){
        return SELF::COL_TIPO_DOCUMENTO;
    }

    /**
     * @return string
     */
    public function getColNumDoc(){
        return SELF::COL_NUM_DOCUMENTO;
    }

    /**
     * @return string
     */
    public function getIdPartnerApi(){
        return SELF::ID_PARTNER_API;
    }

    /**
     * @return string
     */
    public function getPrecioUnitario(){
        return SELF::PRECIO_UNITARIO;
    }

    /**
     * @return string
     */
    public function getQty(){
        return SELF::QTY;
    }

    /**
     * @return string
     */
    public function getTotal(){
        return SELF::TOTAL;
    }

    /**
     * @return string
     */
    public function getSymbolMayor(){
        return SELF::SYMBOL_MAYOR;
    }

    /**
     * @return string
     */
    public function getNombreApi(){
        return SELF::NOMBRE_API;
    }

    /**
     * @return string
     */
    public function getApellidoPaternoApi(){
        return SELF::APELLIDO_PATERNO_API;
    }

    /**
     * @return string
     */
    public function getApellidoMaternoApi(){
        return SELF::APELLIDO_MATERNO_API;
    }

    /**
     * @return string
     */
    public function getEmailApi(){
        return SELF::EMAIL_API;
    }

    /**
     * @return string
     */
    public function getNumTelefonoApi(){
        return SELF::NUM_TELEFONO_API;
    }

    /**
     * @return string
     */
    public function getNumDocumentoApi(){
        return SELF::NUM_DOCUMENTO_API;
    }

    /**
     * @return string
     */
    public function getTipoDocumento(){
        return SELF::TIPO_DOCUMENTO;
    }

    /**
     * @return string
     */
    public function getDireccion(){
        return SELF::DIRECCION;
    }

    /**
     * @return string
     */
    public function getDireccionExtra(){
        return SELF::DIRECCION_EXTRA;
    }

    /**
     * @return string
     */
    public function getLocalizacion(){
        return SELF::LOCALIZACION;
    }

    /**
     * @return string
     */
    public function getMunicipio(){
        return SELF::MUNICIPIO;
    }

    /**
     * @return string
     */
    public function getCiudad(){
        return SELF::CIUDAD;
    }

    /**
     * @return string
     */
    public function getPais(){
        return SELF::PAIS;
    }

    /**
     * @return string
     */
    public function getCustomer(){
        return SELF::COLUMN_CUSTOMER;
    }

    /**
     * @return string
     */
    public function getMunicipalityNone(){
        return SELF::MUNICIPALITY_NONE;
    }

    /**
     * @return string
     */
    public function getCityNone(){
        return SELF::CITY_NONE;
    }

    /**
     * @return string
     */
    public function getCountryNone(){
        return SELF::COUNTRY_NONE;
    }

    /**
     * @return string
     */
    public function getPartnerTokenNone(){
        return SELF::TOKEN_NONE;
    }

    /**
     * @return string
     */
    public function getPartnerNone(){
        return SELF::PARTNER_NONE;
    }

    /**
     * @param string|int $number
     * @param string $label
     * @return string
     */
    public function concatTwoString(string|int $number, string $label){
        return $number." ".$label;
    }

    /**
     * @return string
     */
    public function getTime(){
        return SELF::TIME;
    }

    /**
     * @return string
     */
    public function getDescription(){
        return SELF::DESCRIPTION;
    }

    /**
     * @return string
     */
    public function getCreatedDiference(){
        return SELF::CREATED_DIF;
    }

    /**
     * @return string
     */
    public function getUpdatedDiference(){
        return SELF::UPDATED_DIF;
    }

    /**
     * @param string $text
     * @param int $days
     * @return string
     */
    public function getDiferenceDays(string $text, int $days){
        return $text.str_replace($this->getPercent(), $days, $days > 1 ? SELF::DAYS_DIFENCENS : SELF::DAY_DIFENCENS);
    }

    /**
     * @param string $text
     * @param int $month
     * @return string
     */
    public function getDiferenceMonth(string $text, int $month){
        return $text.str_replace($this->getPercent(), $month, $month > 1 ? SELF::MOTHS_DIFENCENS : SELF::MONTH_DIFENCENS);
    }

    /**
     * @param string $text
     * @param int $year
     * @return string
     */
    public function getDiferenceYear(string $text, int $year){
        return $text.str_replace($this->getPercent(), $year, $year > 1 ? SELF::YEARS_DIFENCENS : SELF::YEAR_DIFENCENS);
    }

    /**
     * @return string
     */
    public function getImage(){
        return SELF::IMAGE;
    }

    /**
     * @return string
     */
    public function getSocialNetwork(){
        return SELF::SOCIAL_NETWORK;
    }

    /**
     * @return string
     */
    public function getStoresId(){
        return SELF::STORES_ID;
    }

    /**
     * @return string
     */
    public function getTablePWIdWarehouse(){
        return SELF::RELATION_WH_P[0];
    }

    /**
     * @return string
     */
    public function getTablePW(){
        return SELF::RELATION_WH_P[1];
    }

    /**
     * @return string
     */
    public function getTablePWProductId(){
        return SELF::RELATION_WH_P[2];
    }

    /**
     * @return string
     */
    public function getPwhIdWarehouse(){
        return SELF::RELATION_WH_P[5];
    }

    /**
     * @return string
     */
    public function getPwhIdProduct(){
        return SELF::RELATION_WH_P[4];
    }

    /**
     * @return string
     */
    public function getEquals(){
        return SELF::RELATION_WH_P[3];
    }

    /**
     * @return string
     */
    public function getProfile(){
        return SELF::PROFILE;
    }

    /**
     * @return string
     */
    public function getCover(){
        return SELF::COVER;
    }

    /**
     * @return string
     */
    public function getIdMetadata(){
        return SELF::ID_METADATA;
    }

    /**
     * @return string
     */
    public function getCatInfo(){
        return SELF::ID_CAT_INFO;
    }

    /**
     * @return string
     */
    public function getUpdateSuccess(){
        return SELF::UPDATE_SUCCESS;
    }

    /**
     * @return string
     */
    public function getInfo(){
        return SELF::INFO;
    }

    /**
     * @return string
     */
    public function getInMenu(){
        return SELF::IN_MENU;
    }

    /**
     * @return string
     */
    public function getTitle(){
        return SELF::TITLE;
    }

    /**
     * @return string
     */
    public function getBody(){
        return SELF::BODY;
    }

    /**
     * @return string
     */
    public function getSubCategoryPos(){
        return SELF::SUB_CAT_POS;
    }

    /**
     * @return string
     */
    public function getTitulo(){
        return SELF::TITULO;
    }

    /**
     * @return string
     */
    public function getLanding(){
        return SELF::LANDING;
    }

    /**
     * @return string
     */
    public function getMetadata(){
        return SELF::METADATA;
    }

    /**
     * @return string
     */
    public function getUrl(){
        return SELF::URL;
    }

    /**
     * @return string
     */
    public function getNoResponse(){
        return SELF::NO_RESPONSE;
    }

    /**
     * @return string
     */
    public function getDescripcion(){
        return SELF::DESCRIPCION;
    }

    /**
     * @return string
     */
    public function getWarehouse(){
        return SELF::WAREHOUSE;
    }

    /**
     * @return string
     */
    public function getSheets(){
        return SELF::SHEETS;
    }

    /**
     * @return string
     */
    public function getWarehouses(){
        return SELF::WAREHOUSES;
    }

    /**
     * @return string
     */
    public function getPrices(){
        return SELF::PRICES;
    }

    /**
     * @return string
     */
    public function getCantidad(){
        return SELF::CANTIDAD;
    }

    /**
     * @return string
     */
    public function getProducts(){
        return SELF::PRODUCTS;
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getNoneSku(string $sku){
        return str_replace($this->getPercent(), $sku, SELF::SKU_NONE);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getNoneIdProduct(int $id){
        return str_replace($this->getPercent(), $id, SELF::ID_NONE);
    }

    /**
     * @return string
     */
    public function getNoExisteSku(){
        return SELF::NO_EXIST_SKU;
    }

    /**
     * @return string
     */
    public function getCuota(){
        return SELF::CUOTA;
    }

    /**
     * @return string
     */
    public function getMonto(){
        return SELF::MONTO;
    }

    /**
     * @return string
     */
    public function getText(){
        return SELF::TEXT;
    }

    /**
     * @return string
     */
    public function getLabel(){
        return SELF::LABEL;
    }

    /**
     * @return string
     */
    public function getGuionBajo(){
        return SELF::GUION_BAJO;
    }

    /**
     * @return string
     */
    public function getCuotas(){
        return SELF::CUOTAS;
    }

    /**
     * @return string
     */
    public function getNamePos(){
        return SELF::NAME_POS;
    }

    /**
     * @return string
     */
    public function getInhitance(){
        return SELF::INHERITANCE;
    }

    /**
     * @return string
     */
    public function getIdPos(){
        return SELF::ID_POS;
    }

    /**
     * @return string
     */
    public function getPosSubCategory(){
        return SELF::POS_SUBCATEGORY;
    }

    /**
     * @return string
     */
    public function getIdCategory(){
        return SELF::ID_CATEGORY;
    }

    /**
     * @return string
     */
    public function getCodigoPadre(){
        return SELF::CODIGO_PADRE;
    }

    /**
     * @return string
     */
    public function getAddOneYear(){
        return SELF::ADD_ONE_YEAR;
    }

    /**
     * @return string
     */
    public function getPrecio(){
        return SELF::PRECIO;
    }

    /**
     * @return string
     */
    public function getDescuento(){
        return SELF::DESCUENTO;
    }

    /**
     * @return string
     */
    public function getListaPrecio(){
        return SELF::LISTA_PRECIO;
    }

    /**
     * @return string
     */
    public function getFromDate(){
        return SELF::FROM_DATE;
    }

    /**
     * @return string
     */
    public function getToDate(){
        return SELF::TO_DATE;
    }

    /**
     * @return string
     */
    public function getSpecialPrice(){
        return SELF::SPECIAL_PRICE;
    }

    /**
     * @return string
     */
    public function getPrice(){
        return SELF::PRICE;
    }

    /**
     * @return string
     */
    public function getAlmacenCentral(){
        return SELF::ALMACEN_CENTRAL;
    }

    /**
     * @return string
     */
    public function getIdPrice(){
        return SELF::ID_PRICE;
    }

    /**
     * @return string
     */
    public function getBase(){
        return SELF::BASE;
    }

    /**
     * @return string
     */
    public function getAlmacen(){
        return SELF::ALMACEN;
    }

    /**
     * @return string
     */
    public function getStock(){
        return SELF::STOCK;
    }

    /**
     * @param int|string|float $value
     * @return int|string|float
     */
    public function convertNumberFormat($value){
        return number_format($value,2,",",".");
    }

    /**
     * @return string
     */
    public function getIdWarehouse(){
        return SELF::ID_WAREHOUSE;
    }

    /**
     * @return string
     */
    public function getIdProduct(){
        return SELF::ID_PRODUCT;
    }

    /**
     * @return string
     */
    public function getStockDisponible(){
        return SELF::STOCKDISPONIBLE;
    }

    /**
     * @return string
     */
    public function getNombreAlmacen(){
        return SELF::NOMBRE_ALMACEN;
    }

    /**
     * @return string
     */
    public function getDisponibilidadPos(){
        return SELF::DISPONIBILIDAD_;
    }

    /**
     * @return string
     */
    public function getPreciosPos(){
        return SELF::PRECIOS_POS;
    }

    /**
     * @return string
     */
    public function getVisible(){
        return SELF::VISIBLE;
    }

    /**
     * @return string
     */
    public function getFiltros(){
        return SELF::FILTROS;
    }

    /**
     * @return string
     */
    public function getMinicuotas(){
        return SELF::MINICUOTAS;
    }

    /**
     * @return string
     */
    public function getEstado(){
        return SELF::ESTADO;
    }

    /**
     * @return string
     */
    public function getClasificacion(){
        return SELF::CLASIFICACION;
    }

    /**
     * @return string
     */
    public function getTextNone(){
        return SELF::TEXT_NONE;
    }

    /**
     * @return string
     */
    public function getTipoProducto(){
        return SELF::TIPO_PRODUCTO;
    }

    /**
     * @return string
     */
    public function getClacom(){
        return SELF::CLACOM;
    }

    /**
     * @return string
     */
    public function getAttributes(){
        return SELF::ATTRIBUTES;
    }

    /**
     * @return string
     */
    public function getBrand(){
        return SELF::BRAND;
    }

    /**
     * @return string
     */
    public function getDetalle(){
        return SELF::DETALLE;
    }

    /**
     * @return string
     */
    public function getNombre(){
        return SELF::NOMBRE;
    }

    /**
     * @return string
     */
    public function getProduct(){
        return SELF::PRODUCT;
    }

    /**
     * @return string
     */
    public function getMarca(){
        return SELF::MARCA;
    }

    /**
     * @return string
     */
    public function getCodigo(){
        return SELF::CODIGO;
    }

    /**
     * @return string
     */
    public function getIdType(){
        return SELF::ID_TYPE;
    }

    /**
     * @return string
     */
    public function getIdClacom(){
        return SELF::ID_CLACOM;
    }

    /**
     * @return string
     */
    public function getIdBrand(){
        return SELF::ID_BRAND;
    }

    /**
     * @return string
     */
    public function getOrderDesc(){
        return SELF::ORDER_DESC;
    }

    /**
     * @return string
     */
    public function getOrderAsc(){
        return SELF::ORDER_ASC;
    }

    /**
     * @return string
     */
    public function getPosAuth(){
        return SELF::POS_AUTH;
    }

    /**
     * @return string
     */
    public function getPosParamOne(){
        return SELF::POS_PARAM_ONE;
    }

    /**
     * @return string
     */
    public function getMethodPost(){
        return SELF::METHOD_POST;
    }

    /**
     * @return string
     */
    public function getMethodGet(){
        return SELF::METHOD_GET;
    }

    /**
     * @return string
     */
    public function getSubCategoria(){
        return SELF::SUB_CATEGORIA;
    }

    /**
     * @return string
     */
    public function getPrecios(){
        return SELF::PRECIOS;
    }

    /**
     * @return string
     */
    public function getDisponibilidad(){
        return SELF::DISPONIBILIDAD;
    }

    /**
     * @return string
     */
    public function getGrupoArticulo(){
        return SELF::GRUPO_ARTICULO;
    }

    /**
     * @return string
     */
    public function getSpace(){
        return SELF::SPACE;
    }

    /**
     * @return string
     */
    public function getRol(){
        return SELF::ROL;
    }

    /**
     * @return string
     */
    public function getAccountStatus(){
        return SELF::ACCOUNT_STATUS;
    }

    /**
     * @return string
     */
    public function getRolAccount(){
        return SELF::ROL_ACCOUNT;
    }

    /**
     * @return string
     */
    public function getDistinctSymbol(){
        return SELF::DISTINCT_SYMBOL;
    }

    /**
     * @return string
     */
    public function getNegativeId(){
        return SELF::NEGATIVE_ID;
    }

    /**
     * @return string
     */
    public function getCategorias(){
        return SELF::CATEGORIAS;
    }

    /**
     * @return string
     */
    public function getPercent(){
        return SELF::PERCENT;
    }

    /**
     * @return string
     */
    public function getLike(){
        return SELF::LIKE;
    }

    /**
     * @return string
     */
    public function getQuery(){
        return SELF::QUERY;
    }

    /**
     * @return string
     */
    public function getProductos(){
        return SELF::PRODUCTOS;
    }

    /**
     * @return string
     */
    public function getStores(){
        return SELF::STORES;
    }

    /**
     * @return string
     */
    public function getCategoryNone(){
        return SELF::CATEGORY_NONE;
    }

    /**
     * @return null
     */
    public function isNullType(){
        return SELF::NULL_TYPE;
    }

    /**
     * @return string
     */
    public function getAccountNotExist(){
        return SELF::ACCOUNT_NOT;
    }

    /**
     * @return string
     */
    public function getAccountExist(){
        return SELF::ACCOUNT_YES;
    }

    /**
     * @return string
     */
    public function getQuerySuccess(){
        return SELF::QUERY_SUCCESS;
    }

    /**
     * @return string
     */
    public function getCatalogNoExist(){
        return SELF::CATALOG_NOEXIST;
    }

    /**
     * @return string
     */
    public function getNoneFilter(){
        return SELF::NONE_FILTER;
    }

    /**
     * @param bool|array|null|int|string|float $status
     * @param string $response
     * @return array
     */
    public function getResponseApi(bool|array|null|int|string|float $status, string $response){
        return array(
            SELF::RESPONSE => $status,
            SELF::RESPONSE_TEXT => $response
        );
    }

    /**
     * @return string
     */
    public function getCatalogExist(){
        return SELF::CATALOG_EXIST;
    }

    /**
     * @return string
     */
    public function getAccountRegister(){
        return SELF::ACCOUNT_REGIS;
    }

    /**
     * @return string
     */
    public function getErrorSearch(){
        return SELF::SEARCH_ERROR;
    }

    /**
     * @return string
     */
    public function getSuccessSearch(){
        return SELF::SEARCH_SUCCESS;
    }

    /**
     * @return string
     */
    public function getErrorParametros(){
        return SELF::ERROR_PARAMETRO;
    }

    /**
     * @return string
     */
    public function getObject(){
        return SELF::OBJECT;
    }

    /**
     * @return string
     */
    public function getImportSuccess(){
        return SELF::IMPORT_SUCCESS;
    }

    /**
     * @return string
     */
    public function getSku(){
        return SELF::SKU;
    }
    
    /**
     * @return string
     */
    public function getSkuApi(){
        return SELF::SKU_API;
    }

    /**
     * @return string
     */
    public function getIdStore(){
        return SELF::ID_STORE;
    }

    /**
     * @return string
     */
    public function getStoreName(){
        return SELF::STORE_NAME;
    }

    /**
     * @return string
     */
    public function getIdCatalog(){
        return SELF::ID_CATALOG;
    }

    /**
     * @return string
     */
    public function getIdPartner(){
        return SELF::ID_PARTNER;
    }

    /**
     * @return string
     */
    public function getNonePartner(){
        return SELF::Partner_None;
    }

    /**
     * @return string
     */
    public function getValue(){
        return SELF::VALUE;
    }

    /**
     * @return string
     */
    public function getKey(){
        return SELF::KEY;
    }

    /**
     * @return string
     */
    public function getType(){
        return SELF::TYPE;
    }

    /**
     * @return string
     */
    public function getAccountDisable(){
        return SELF::Disable;
    }

    /**
     * @return string
     */
    public function getAccountEnable(){
        return SELF::Enable;
    }

    /**
     * @return string
     */
    public function invalidFormatUser(){
        return SELF::MESSAGES_LOGN[5];
    }

    /**
     * @return string
     */
    public function AccountNotExist(){
        return SELF::MESSAGES_LOGN[6];
    }

    /**
     * @return string
     */
    public function getStore(){
        return SELF::STORE;
    }

    /**
     * @return string
     */
    public function getStatus(){
        return SELF::STATUS;
    }

    /**
     * @return string
     */
    public function getIdAccount(){
        return SELF::ID_ACCOUNT;
    }

    /**
     * @return string
     */
    public function getEncryptMethod(){
        return SELF::ENCRYP_METHOD;
    }

    /**
     * @return string
     */
    public function getEncryptKey(){
        return SELF::ENCRYP_KEY;
    }

    /**
     * @param bool $status
     * @param int $position
     * @param string $token
     * @return array
     */
    public function messageLogin(bool $status,int $position, string $token = null){
        return array(
            $this->getStatus() => $status,
            $this->getText() => SELF::MESSAGES_LOGN[$position],
            $this->getToken() => $token
        );
    }

    /**
     * @return string
     */
    public function getArroba(){
        return SELF::ARROBA;
    }

    /**
     * @return string
     */
    public function getIdRolPermissions(){
        return SELF::ID_PERMISSIONS;
    }

    /**
     * @return string
     */
    public function getIdRol(){
        return SELF::ID_ROL;
    }

    /**
     * @return string
     */
    public function getCode(){
        return SELF::CODE;
    }

    /**
     * @return string
     */
    public function getIntegrationsApi(){
        return SELF::INTEGRATION;
    }

    /**
     * @return string
     */
    public function getMunicipality(){
        return SELF::MUNICIPALITY;
    }

    /**
     * @return string
     */
    public function getCountry(){
        return SELF::COUNTRY;
    }

    /**
     * @return string
     */
    public function getCity(){
        return SELF::CITY;
    }

    /**
     * @return string
     */
    public function getEmailAlready(){
        return SELF::EMAIL_ALREADY;
    }

    /**
     * @return string
     */
    public function getUsername(){
        return SELF::USERNAME;
    }

    /**
     * @return string
     */
    public function getPassword(){
        return SELF::PASSWORD;
    }

    /**
     * @return string
     */
    public function getRazonSocial(){
        return SELF::RAZON_SOCIAL;
    }

    /**
     * @return string
     */
    public function getLegalRepresentative(){
        return SELF::L_REPRESENTA;
    }

    /**
     * @return string
     */
    public function getNit(){
        return SELF::NIT;
    }

    /**
     * @return string
     */
    public function getEmail(){
        return SELF::EMAIL;
    }

    /**
     * @return string
     */
    public function getPartnerAlready(){
        return SELF::PARTNER_ALREADY;
    }

    /**
     * @return string
     */
    public function getRounds(){
        return SELF::ROUNDS;
    }

    /**
     * @return string
     */
    public function getCero(){
        return SELF::CERO;
    }

    /**
     * @return string
     */
    public function getComa(){
        return SELF::COMA;
    }

    /**
     * @return string
     */
    public function getBarraJson(){
        return SELF::BARRA_JSON;
    }

    /**
     * @return string
     */
    public function getLocalhost(){
        return SELF::LOCALHOST;
    }

    /**
     * @return string
     */
    public function getIpHost(){
        return SELF::IP_HOST;
    }

    /**
     * @return string
     */
    public function getIdLocalization(){
        return SELF::ID_LOCALIZATION;
    }

    /**
     * @return string
     */
    public function getIdAddressExtra(){
        return SELF::IDADDRESS_EXTRA;
    }

    /**
     * @return string
     */
    public function getAddressExtra(){
        return SELF::ADDRESS_EXTRA;
    }

    /**
     * @return string
     */
    public function getIdMunicipality(){
        return SELF::ID_MUNICIPALITY;
    }

    /**
     * @return string
     */
    public function getIdCountry(){
        return SELF::ID_COUNTRY;
    }

    /**
     * @return string
     */
    public function getLongitud(){
        return SELF::LONGITUD;
    }

    /**
     * @return string
     */
    public function getLongitude(){
        return SELF::LONGITUDE;
    }

    /**
     * @return string
     */
    public function getLatitud(){
        return SELF::LATITUD;
    }

    /**
     * @return string
     */
    public function getLatitude(){
        return SELF::LATITUDE;
    }

    /**
     * @return string
     */
    public function getUpdated(){
        return SELF::COLUMN_UPDATED;
    }

    /**
     * @return string
     */
    public function getCreated(){
        return SELF::COLUMN_CREATED;
    }

    /**
     * @return string
     */
    public function getDomain(){
        return SELF::COLUMN_DOMAIN;
    }

    /**
     * @return string
     */
    public function getToken(){
        return SELF::COLUMN_TOKEN;
    }

    /**
     * @return string
     */
    public function getIdCity(){
        return SELF::COLUMN_ID_CITY;
    }

    /**
     * @return string
     */
    public function getId(){
        return SELF::COLUMN_ID;
    }

    /**
     * @return string
     */
    public function getName(){
        return SELF::COLUMN_NAME;
    }

    /**
     * @return string
     */
    public function getAccessDecline(){
        return SELF::ACCESS_DECLINE;
    }

    /**
     * @return string
     */
    public function getTokenDecline(){
        return SELF::TOKEN_DECLINE;
    }

    /**
     * @return string
     */
    public function getAuthorization(){
        return SELF::AUTHORIZATION;
    }

    /**
     * @return string
     */
    public function getDisplayError(){
        return SELF::DISPLAY_ERROR;
    }

    /**
     * @return string
     */
    public function getMailCc(){
        return SELF::MAIL_CC;
    }

    /**
     * @return string
     */
    public function getLine(){
        return SELF::LINE_LINE;
    }

    /**
     * @return string
     */
    public function getMailFrom(){
        return SELF::MAIL_FROM;
    }

    /**
     * @return string
     */
    public function getMailReply(){
        return SELF::MAIL_REPLY;
    }

    /**
     * @return string
     */
    public function getMailHeaders(){
        return SELF::MAIL_HEADERS;
    }

    /**
     * @return string
     */
    public function getAddSuccess(){
        return SELF::ADD_SUCCESS;
    }
    
    /**
     * @return string
     */
    public function getExtra(){
        return SELF::COLUMN_EXTRA;
    }

    /**
     * @return string
     */
    public function getAddress(){
        return SELF::COLUMN_ADDRESS;
    }
    
    /**
     * @return string
     */
    public function getPartner(){
        return SELF::COLUMN_PARTNER;
    }

    /**
     * @return string
     */
    public function getMedidaComercial(){
        return SELF::MED_COMERCIAL;
    }
    
    /**
     * @return string
     */
    public function getCuotaInicial(){
        return SELF::INICIAL;
    }
    
    /**
     * @return string
     */
    public function getCustom(){
        return SELF::CUSTOM;
    }
    
    /**
     * @return string
     */
    public function getAccount(){
        return SELF::COLUMN_ACCOUNT;
    }
}

?>