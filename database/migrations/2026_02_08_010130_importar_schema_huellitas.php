<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // TODAS las Tablas
        DB::unprepared("
            SET FOREIGN_KEY_CHECKS = 0;

            -- 1) CATÁLOGOS BASE
            CREATE TABLE IF NOT EXISTS especies (
                id_especie BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(60) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_especies_nombre (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS razas (
                id_raza BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                especie_id BIGINT UNSIGNED NOT NULL,
                nombre VARCHAR(80) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_raza_especie_nombre (especie_id, nombre),
                CONSTRAINT fk_razas_especie FOREIGN KEY (especie_id) REFERENCES especies(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 2) USUARIOS + CONFIG
            CREATE TABLE IF NOT EXISTS usuarios (
                id_usuario BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                correo VARCHAR(120) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                rol ENUM('USUARIO','ADMIN','VETERINARIA','REFUGIO') NOT NULL DEFAULT 'USUARIO',
                nombre VARCHAR(120) NOT NULL,
                telefono CHAR(10) NOT NULL,
                whatsapp CHAR(10) NULL,
                fecha_nac DATE NULL,
                ciudad VARCHAR(100) NULL,
                foto_perfil VARCHAR(255) NULL,
                estado ENUM('ACTIVA','SUSPENDIDA','ELIMINADA') NOT NULL DEFAULT 'ACTIVA',
                motivo_estado VARCHAR(255) NULL,
                ultimo_login_en DATETIME NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                eliminado_en DATETIME NULL,
                UNIQUE KEY uq_usuarios_correo (correo),
                KEY idx_usuarios_rol (rol),
                CHECK (telefono REGEXP '^[0-9]{10}$'),
                CHECK (whatsapp IS NULL OR whatsapp REGEXP '^[0-9]{10}$')
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS usuario_configuracion (
                id_config BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                usuario_id BIGINT UNSIGNED NOT NULL,
                recibir_notificaciones BOOLEAN NOT NULL DEFAULT 1,
                recibir_correos BOOLEAN NOT NULL DEFAULT 1,
                mostrar_telefono_publico BOOLEAN NOT NULL DEFAULT 0,
                mostrar_whatsapp_publico BOOLEAN NOT NULL DEFAULT 0,
                ocultar_ubicacion_exacta BOOLEAN NOT NULL DEFAULT 1,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_user_config_usuario (usuario_id),
                CONSTRAINT fk_user_config_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 3) DIRECCIONES / UBICACIONES
            CREATE TABLE IF NOT EXISTS direcciones (
                id_direccion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                calle_numero VARCHAR(150) NOT NULL,
                colonia VARCHAR(100) NOT NULL,
                codigo_postal VARCHAR(10) NOT NULL,
                ciudad VARCHAR(100) NOT NULL,
                estado VARCHAR(100) NOT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_direcciones_cp (codigo_postal)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS ubicaciones (
                id_ubicacion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                latitud DECIMAL(10,8) NOT NULL,
                longitud DECIMAL(11,8) NOT NULL,
                precision_metros INT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CHECK (latitud BETWEEN -90 AND 90),
                CHECK (longitud BETWEEN -180 AND 180)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 4) ORGANIZACIONES Y DETALLES
            CREATE TABLE IF NOT EXISTS organizaciones (
                id_organizacion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                tipo ENUM('VETERINARIA','REFUGIO') NOT NULL,
                usuario_dueno_id BIGINT UNSIGNED NOT NULL,
                nombre VARCHAR(150) NOT NULL,
                descripcion TEXT NOT NULL,
                telefono CHAR(10) NOT NULL,
                whatsapp CHAR(10) NULL,
                sitio_web VARCHAR(200) NULL,
                direccion_id BIGINT UNSIGNED NOT NULL,
                ubicacion_id BIGINT UNSIGNED NOT NULL,
                estado_revision ENUM('PENDIENTE','APROBADA','RECHAZADA') NOT NULL DEFAULT 'PENDIENTE',
                revisado_por BIGINT UNSIGNED NULL,
                revisado_en DATETIME NULL,
                motivo_rechazo VARCHAR(255) NULL,
                puede_reintentar_desde DATE NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_org_dueno FOREIGN KEY (usuario_dueno_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_org_direccion FOREIGN KEY (direccion_id) REFERENCES direcciones(id_direccion) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_org_ubicacion FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones(id_ubicacion) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS veterinaria_detalle (
                organizacion_id BIGINT UNSIGNED PRIMARY KEY,
                medico_responsable VARCHAR(150) NOT NULL,
                cedula_profesional VARCHAR(50) NOT NULL,
                num_veterinarios INT NULL,
                otros_servicios TEXT NULL,
                CONSTRAINT fk_vet_det_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS refugio_detalle (
                organizacion_id BIGINT UNSIGNED PRIMARY KEY,
                tipo_organizacion VARCHAR(120) NOT NULL,
                anio_fundacion SMALLINT NULL,
                capacidad_total INT NOT NULL,
                animales_actuales INT NOT NULL,
                animales_dados_adopcion INT NOT NULL,
                anios_operacion INT NOT NULL,
                nombre_responsable VARCHAR(150) NOT NULL,
                cargo_responsable VARCHAR(120) NOT NULL,
                num_voluntarios INT NOT NULL DEFAULT 0,
                otras_especies TEXT NULL,
                CONSTRAINT fk_ref_det_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS organizacion_red_social (
                id_red BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                plataforma ENUM('FACEBOOK','INSTAGRAM','TIKTOK','X','YOUTUBE','OTRO') NOT NULL DEFAULT 'OTRO',
                url VARCHAR(255) NOT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_org_red_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS horarios_atencion (
                id_horario BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                dia_semana TINYINT UNSIGNED NOT NULL,
                hora_apertura TIME NULL,
                hora_cierre TIME NULL,
                cerrado BOOLEAN NOT NULL DEFAULT 0,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_horario_org_dia (organizacion_id, dia_semana),
                CONSTRAINT fk_horario_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS organizacion_fotos (
                id_foto BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                url VARCHAR(255) NOT NULL,
                orden TINYINT UNSIGNED NOT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_org_foto_orden (organizacion_id, orden),
                CONSTRAINT fk_org_foto_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 5) SERVICIOS Y COSTOS
            CREATE TABLE IF NOT EXISTS servicios (
                id_servicio BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(80) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_servicio_nombre (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS servicios_costeables (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                servicio_id BIGINT UNSIGNED NOT NULL,
                obligatorio_en_registro BOOLEAN NOT NULL DEFAULT 0,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_servicio_costeable (servicio_id),
                CONSTRAINT fk_serv_costeable_serv FOREIGN KEY (servicio_id) REFERENCES servicios(id_servicio) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS organizacion_servicio (
                id_org_servicio BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                servicio_id BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_org_servicio (organizacion_id, servicio_id),
                CONSTRAINT fk_org_serv_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_org_serv_serv FOREIGN KEY (servicio_id) REFERENCES servicios(id_servicio) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS organizacion_costo_servicio (
                id_costo BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                servicio_id BIGINT UNSIGNED NOT NULL,
                precio DECIMAL(10,2) NOT NULL,
                moneda CHAR(3) NOT NULL DEFAULT 'MXN',
                nota VARCHAR(200) NULL,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_org_costo_servicio (organizacion_id, servicio_id),
                CONSTRAINT fk_org_cost_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_org_cost_serv FOREIGN KEY (servicio_id) REFERENCES servicios(id_servicio) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 6) ESPECIES Y RESEÑAS
            CREATE TABLE IF NOT EXISTS refugio_especie (
                id_refugio_especie BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                especie_id BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_refugio_especie (organizacion_id, especie_id),
                CONSTRAINT fk_ref_especie_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ref_especie_especie FOREIGN KEY (especie_id) REFERENCES especies(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS resenas (
                id_resena BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                organizacion_id BIGINT UNSIGNED NOT NULL,
                usuario_id BIGINT UNSIGNED NOT NULL,
                calificacion TINYINT UNSIGNED NOT NULL,
                comentario TEXT NULL,
                estado ENUM('VISIBLE','OCULTO','ELIMINADO') NOT NULL DEFAULT 'VISIBLE',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_resena_org_usuario (organizacion_id, usuario_id),
                CONSTRAINT fk_resena_org FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_resena_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 8) CONSEJOS
            CREATE TABLE IF NOT EXISTS categorias_consejo (
                id_categoria BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(60) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_categoria_consejo (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS etiquetas (
                id_etiqueta BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(60) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_etiqueta_nombre (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS consejos (
                id_consejo BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                autor_organizacion_id BIGINT UNSIGNED NOT NULL,
                titulo VARCHAR(100) NOT NULL,
                resumen VARCHAR(200) NOT NULL,
                categoria_id BIGINT UNSIGNED NOT NULL,
                especie_id BIGINT UNSIGNED NOT NULL,
                contenido LONGTEXT NOT NULL,
                estado_publicacion ENUM('PENDIENTE','APROBADO','RECHAZADO') NOT NULL DEFAULT 'PENDIENTE',
                revisado_por BIGINT UNSIGNED NULL,
                revisado_en DATETIME NULL,
                motivo_rechazo VARCHAR(255) NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_consejo_autor_org FOREIGN KEY (autor_organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_consejo_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_consejo(id_categoria) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_consejo_especie FOREIGN KEY (especie_id) REFERENCES especies(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_consejo_revisado_por FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS consejo_etiqueta (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                consejo_id BIGINT UNSIGNED NOT NULL,
                etiqueta_id BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_consejo_etiqueta (consejo_id, etiqueta_id),
                CONSTRAINT fk_consejo_et_consejo FOREIGN KEY (consejo_id) REFERENCES consejos(id_consejo) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_consejo_et_etiqueta FOREIGN KEY (etiqueta_id) REFERENCES etiquetas(id_etiqueta) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS consejo_imagen (
                id_imagen BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                consejo_id BIGINT UNSIGNED NOT NULL,
                url VARCHAR(255) NOT NULL,
                orden TINYINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_consejo_img_orden (consejo_id, orden),
                CONSTRAINT fk_consejo_img_consejo FOREIGN KEY (consejo_id) REFERENCES consejos(id_consejo) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 9) ADOPCIÓN
            CREATE TABLE IF NOT EXISTS publicaciones_adopcion (
                id_publicacion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                autor_usuario_id BIGINT UNSIGNED NULL,
                autor_organizacion_id BIGINT UNSIGNED NULL,
                nombre VARCHAR(100) NULL,
                especie_id BIGINT UNSIGNED NOT NULL,
                raza_id BIGINT UNSIGNED NULL,
                otra_raza VARCHAR(80) NULL,
                edad_anios TINYINT UNSIGNED NULL,
                sexo ENUM('MACHO','HEMBRA','DESCONOCIDO') NOT NULL DEFAULT 'DESCONOCIDO',
                tamano ENUM('CHICO','MEDIANO','GRANDE','DESCONOCIDO') NOT NULL DEFAULT 'DESCONOCIDO',
                color_predominante VARCHAR(80) NULL,
                descripcion TEXT NOT NULL,
                vacunas_aplicadas TEXT NULL,
                esterilizado BOOLEAN NOT NULL DEFAULT 0,
                condicion_salud VARCHAR(120) NULL,
                descripcion_salud TEXT NULL,
                requisitos TEXT NULL,
                colonia_barrio VARCHAR(120) NOT NULL,
                calle_referencias VARCHAR(200) NULL,
                ubicacion_id BIGINT UNSIGNED NULL,
                estado ENUM('DISPONIBLE','EN_PROCESO','ADOPTADA','PAUSADA','ELIMINADA') NOT NULL DEFAULT 'DISPONIBLE',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                eliminado_en DATETIME NULL,
                CONSTRAINT fk_adop_autor_usuario FOREIGN KEY (autor_usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_adop_autor_org FOREIGN KEY (autor_organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_adop_especie FOREIGN KEY (especie_id) REFERENCES especies(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_adop_raza FOREIGN KEY (raza_id) REFERENCES razas(id_raza) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS adopcion_fotos (
                id_foto BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                publicacion_id BIGINT UNSIGNED NOT NULL,
                url VARCHAR(255) NOT NULL,
                orden TINYINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_adop_foto_orden (publicacion_id, orden),
                CONSTRAINT fk_adop_foto_pub FOREIGN KEY (publicacion_id) REFERENCES publicaciones_adopcion(id_publicacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS solicitudes_adopcion (
                id_solicitud BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                publicacion_id BIGINT UNSIGNED NOT NULL,
                solicitante_usuario_id BIGINT UNSIGNED NOT NULL,
                nombre_completo VARCHAR(150) NOT NULL,
                edad TINYINT UNSIGNED NOT NULL,
                telefono CHAR(10) NOT NULL,
                correo VARCHAR(120) NOT NULL,
                ocupacion VARCHAR(120) NOT NULL,
                estado_civil ENUM('SOLTERO','CASADO','UNION_LIBRE','PREFIERO_NO_RESPONDER') NOT NULL DEFAULT 'PREFIERO_NO_RESPONDER',
                tipo_vivienda ENUM('CASA','DEPARTAMENTO','CUARTO','OTRO') NOT NULL DEFAULT 'CASA',
                propia_o_rentada ENUM('PROPIA','RENTADA','FAMILIAR') NOT NULL DEFAULT 'PROPIA',
                tiene_patio BOOLEAN NOT NULL,
                num_integrantes ENUM('1','2','3','4','5','6+') NOT NULL DEFAULT '1',
                todos_de_acuerdo BOOLEAN NOT NULL,
                tuvo_mascotas BOOLEAN NOT NULL,
                tipo_mascotas_antes VARCHAR(200) NULL,
                tiene_mascotas_actualmente BOOLEAN NOT NULL,
                mascotas_actuales_detalle VARCHAR(200) NULL,
                motivo_adopcion TEXT NOT NULL,
                cubrir_gastos_vet BOOLEAN NOT NULL,
                horas_sola VARCHAR(60) NOT NULL,
                que_haria_problemas_comportamiento TEXT NOT NULL,
                comentarios_adicionales TEXT NULL,
                estado ENUM('ENVIADA','ACEPTADA','RECHAZADA','CANCELADA') NOT NULL DEFAULT 'ENVIADA',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                respondido_en DATETIME NULL,
                UNIQUE KEY uq_sol_por_usuario (publicacion_id, solicitante_usuario_id),
                CONSTRAINT fk_sol_pub FOREIGN KEY (publicacion_id) REFERENCES publicaciones_adopcion(id_publicacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_sol_user FOREIGN KEY (solicitante_usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 11) EXTRAVÍO
            CREATE TABLE IF NOT EXISTS publicaciones_extravio (
                id_publicacion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                autor_usuario_id BIGINT UNSIGNED NULL,
                autor_organizacion_id BIGINT UNSIGNED NULL,
                nombre VARCHAR(100) NULL,
                especie_id BIGINT UNSIGNED NOT NULL,
                raza_id BIGINT UNSIGNED NULL,
                otra_raza VARCHAR(80) NULL,
                color VARCHAR(80) NOT NULL,
                tamano ENUM('CHICO','MEDIANO','GRANDE','DESCONOCIDO') NOT NULL DEFAULT 'DESCONOCIDO',
                sexo ENUM('MACHO','HEMBRA','DESCONOCIDO') NOT NULL DEFAULT 'DESCONOCIDO',
                fecha_extravio DATE NOT NULL,
                colonia_barrio VARCHAR(120) NOT NULL,
                calle_referencias VARCHAR(200) NOT NULL,
                ubicacion_id BIGINT UNSIGNED NULL,
                descripcion TEXT NOT NULL,
                estado ENUM('ACTIVA','RESUELTA','ELIMINADA') NOT NULL DEFAULT 'ACTIVA',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                resuelta_en DATETIME NULL,
                eliminado_en DATETIME NULL,
                CONSTRAINT fk_ext_autor_usuario FOREIGN KEY (autor_usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_ext_autor_org FOREIGN KEY (autor_organizacion_id) REFERENCES organizaciones(id_organizacion) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_ext_especie FOREIGN KEY (especie_id) REFERENCES especies(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS extravio_fotos (
                id_foto BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                publicacion_id BIGINT UNSIGNED NOT NULL,
                url VARCHAR(255) NOT NULL,
                orden TINYINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_ext_foto_orden (publicacion_id, orden),
                CONSTRAINT fk_ext_foto_pub FOREIGN KEY (publicacion_id) REFERENCES publicaciones_extravio(id_publicacion) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS comentarios_extravio (
                id_comentario BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                publicacion_id BIGINT UNSIGNED NOT NULL,
                usuario_id BIGINT UNSIGNED NOT NULL,
                comentario_padre_id BIGINT UNSIGNED NULL,
                comentario TEXT NOT NULL,
                estado ENUM('VISIBLE','OCULTO','ELIMINADO') NOT NULL DEFAULT 'VISIBLE',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_com_ext_pub FOREIGN KEY (publicacion_id) REFERENCES publicaciones_extravio(id_publicacion) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_com_ext_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_com_ext_padre FOREIGN KEY (comentario_padre_id) REFERENCES comentarios_extravio(id_comentario) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 12) REPORTES
            CREATE TABLE IF NOT EXISTS motivos_reporte (
                id_motivo BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(120) NOT NULL,
                activo BOOLEAN NOT NULL DEFAULT 1,
                UNIQUE KEY uq_motivo_nombre (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS reportes (
                id_reporte BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                reportante_usuario_id BIGINT UNSIGNED NOT NULL,
                objetivo_tipo ENUM('PUB_EXTRAVIO','COM_EXTRAVIO','PUB_ADOPCION','RESENA','CONSEJO') NOT NULL,
                objetivo_id BIGINT UNSIGNED NOT NULL,
                motivo_id BIGINT UNSIGNED NOT NULL,
                descripcion_adicional TEXT NULL,
                estado ENUM('ENVIADO','EN_REVISION','RESUELTO','DESCARTADO') NOT NULL DEFAULT 'ENVIADO',
                revisado_por BIGINT UNSIGNED NULL,
                revisado_en DATETIME NULL,
                nota_resolucion VARCHAR(255) NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_rep_obj (objetivo_tipo, objetivo_id),
                KEY idx_rep_estado (estado),
                CONSTRAINT fk_rep_reportante FOREIGN KEY (reportante_usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_rep_motivo FOREIGN KEY (motivo_id) REFERENCES motivos_reporte(id_motivo) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_rep_revisado_por FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- 13) CONTACTO Y NOTIFICACIONES
            CREATE TABLE IF NOT EXISTS mensajes_contacto (
                id_mensaje BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre_completo VARCHAR(150) NOT NULL,
                correo VARCHAR(120) NOT NULL,
                mensaje TEXT NOT NULL,
                estado ENUM('NUEVO','LEIDO','RESPONDIDO') NOT NULL DEFAULT 'NUEVO',
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS notificaciones (
                id_notificacion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                usuario_id BIGINT UNSIGNED NOT NULL,
                tipo ENUM('SISTEMA','APROBACION','ADOPCION','REPORTE','CONSEJO') NOT NULL DEFAULT 'SISTEMA',
                titulo VARCHAR(120) NOT NULL,
                mensaje TEXT NOT NULL,
                leido BOOLEAN NOT NULL DEFAULT 0,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            SET FOREIGN_KEY_CHECKS = 1;
        ");

        // 2. Crear Triggers (Separados)
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_adop_autor_bi;
            CREATE TRIGGER trg_adop_autor_bi BEFORE INSERT ON publicaciones_adopcion FOR EACH ROW
            BEGIN
                IF (NEW.autor_usuario_id IS NULL AND NEW.autor_organizacion_id IS NULL)
                OR (NEW.autor_usuario_id IS NOT NULL AND NEW.autor_organizacion_id IS NOT NULL) THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Adopcion: Debe especificar autor_usuario_id O autor_organizacion_id (exactamente uno).';
                END IF;
            END;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_ext_autor_bi;
            CREATE TRIGGER trg_ext_autor_bi BEFORE INSERT ON publicaciones_extravio FOR EACH ROW
            BEGIN
                IF (NEW.autor_usuario_id IS NULL AND NEW.autor_organizacion_id IS NULL)
                OR (NEW.autor_usuario_id IS NOT NULL AND NEW.autor_organizacion_id IS NOT NULL) THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Extravío: Debe especificar autor_usuario_id O autor_organizacion_id (exactamente uno).';
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(255) NOT NULL,
                user_id BIGINT UNSIGNED DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                user_agent TEXT DEFAULT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,
                PRIMARY KEY (id),
                KEY sessions_user_id_index (user_id),
                KEY sessions_last_activity_index (last_activity)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } 

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Borrar todas las tablas en orden inverso para evitar errores de FK
        DB::unprepared('
            DROP TABLE IF EXISTS 
            notificaciones, mensajes_contacto, reportes, motivos_reporte,
            comentarios_extravio, extravio_fotos, publicaciones_extravio,
            solicitudes_adopcion, adopcion_fotos, publicaciones_adopcion,
            consejo_imagen, consejo_etiqueta, consejos, etiquetas, categorias_consejo,
            resenas, refugio_especie, organizacion_costo_servicio, organizacion_servicio,
            servicios_costeables, servicios, organizacion_fotos, horarios_atencion,
            organizacion_red_social, refugio_detalle, veterinaria_detalle, organizaciones,
            ubicaciones, direcciones, usuario_configuracion, usuarios, razas, especies
        ');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }


};