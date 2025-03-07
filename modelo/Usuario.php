<?php
include 'Conexion.php';

class Usuario
{
    var $objetos;
    public function __construct()
    {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    function crear($nombre, $contrasena, $cedula, $correo, $horasR, $curso, $telefono)
    {
        $sql = "    SELECT id_usuario
                    FROM usuario
                    WHERE id_usuario=:cedula
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':cedula' => $cedula
        ));
        $this->objetos = $query->fetchall();
        if (!empty($this->objetos)) {
            echo 'noAdd';
        } else {
            $sql = "    INSERT INTO usuario(id_usuario, nombre_usuario, correoIns_usuario, horasNecesarias_usuario, horasRealizadas_usuario, contras_usuario, cursos_id_crs, tipoUsuario_id_tipoUsuario, tel_usuario)
                        VALUES(:cedula, :nombre, :correo,:horasN, :horasR, :contrasena, :curso, :us_tipo, :telefono)
            ";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
                ':cedula'    => $cedula,
                ':nombre'    => $nombre,
                ':correo'    => $correo,
                ':horasR'    => $horasR,
                ':contrasena' => $contrasena,
                ':curso'    => $curso,
                ':telefono'    => $telefono,
                ':horasN'    => 160,
                ':us_tipo'    => 2

            ));
            $this->objetos = $query->fetchall();
            echo 'add';
        }
    }

    function editar($id_us, $nombre, $contrasena, $correo)
    {
        $sql = "    UPDATE  Usuario SET nombre_usuario = :nombre, correoIns_usuario = :correo, contras_usuario = :contrasena
                        WHERE id_usuario = :id
            ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':nombre'    => $nombre,
            ':contrasena' => $contrasena,
            ':correo' => $correo,
            ':id'    => $id_us

        ));
        $this->objetos = $query->fetchall();
        echo 'edit';
    }

    function buscar()
    {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT id_usuario, nombre_usuario, correoIns_usuario, horasNecesarias_usuario, horasRealizadas_usuario, nombre_crs, tel_usuario FROM usuario
            JOIN curso on cursos_id_crs=id_crs
            WHERE nombre_usuario LIKE :consulta
            OR correoIns_usuario LIKE :consulta 
            OR id_usuario LIKE :consulta
            OR nombre_crs LIKE :consulta
            AND tipoUsuario_id_tipoUsuario = 2
            AND estado_usuario = 1
                        ";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
                ':consulta' => "%$consulta%"
            ));
            $this->objetos = $query->fetchall();
            return $this->objetos;
        } else {
            $sql = "SELECT id_usuario, nombre_usuario, correoIns_usuario, horasNecesarias_usuario, horasRealizadas_usuario, nombre_crs, tel_usuario FROM usuario
                    JOIN curso on cursos_id_crs=id_crs
                    WHERE nombre_usuario NOT LIKE ''
                    AND tipoUsuario_id_tipoUsuario = 2
                    AND estado_usuario = 1
                    ORDER BY nombre_usuario ASC
                    LIMIT 25                
                    ";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchall();
            return $this->objetos;
        }
    }

    function buscarFin()
    {
        $sql = "SELECT id_usuario, nombre_usuario, correoIns_usuario, horasNecesarias_usuario, horasRealizadas_usuario, nombre_crs, tel_usuario FROM usuario
                    JOIN curso on cursos_id_crs=id_crs
                    WHERE horasRealizadas_usuario = 160
                    AND tipoUsuario_id_tipoUsuario = 2
                    AND estado_usuario = 1
                    ORDER BY nombre_usuario ASC
                    LIMIT 25                
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function loguearse($us, $pass)
    {
        $sql = "  SELECT * 
                    FROM usuario
                    JOIN curso ON cursos_id_crs = id_crs
                    WHERE correoIns_usuario=:n_usuario
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':n_usuario' => $us));
        $objetos = $query->fetchall();
        if (!empty($objetos)) {
            foreach ($objetos as $objeto) {
                $contrasena_actual = $objeto->contras_usuario;
            }
            if ($pass == $contrasena_actual) {
                return "logueado";
            }
        } else {
            return null;
        }
    }

    function obtenerDatosLogueo($user)
    {
        $sql = "  SELECT * 
                    FROM usuario
                    JOIN tipoUsuario on tipoUsuario_id_tipoUsuario=id_tipoUsuario
                    JOIN curso ON cursos_id_crs = id_crs
                    WHERE correoIns_usuario=:n_usuario";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':n_usuario' => $user));
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function obtenerDatos($id)
    {
        $sql = "  SELECT * 
                    FROM usuario
                    JOIN tipoUsuario on tipoUsuario_id_tipoUsuario=id_tipoUsuario
                    AND id_usuario=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function buscar_us_id($id)
    {
        $sql = "SELECT * FROM usuario
                JOIN curso on cursos_id_crs=id_crs
                    WHERE id_usuario = :id
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => "$id"
        ));
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function buscar_crs_est($id)
    {
        $sql = "SELECT id_usuario, nombre_usuario FROM usuario
                WHERE cursos_id_crs=:id
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => $id
        ));
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function buscar_us_crs($id)
    {
        $sql = "SELECT id_usuario,nombre_usuario, correoIns_usuario,horasNecesarias_usuario, 
                horasRealizadas_usuario, tel_usuario, nombre_crs
                FROM usuario JOIN curso on cursos_id_crs=id_crs
                WHERE estado_usuario = 1 
                AND cursos_id_crs =:id
                    ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => "$id"
        ));
        $this->objetos = $query->fetchall();
        return $this->objetos;
    }

    function agregarHoras($id_usuario, $duracion)
    {
        //Inserción de datos
        $sql = "UPDATE usuario SET horasRealizadas_usuario = horasRealizadas_usuario + :duracion 
                    WHERE id_usuario = :id_usuario
            ";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':duracion' => $duracion,
            ':id_usuario'  => $id_usuario,
        ));
        $this->objetos = $query->fetchall();
    }

    function act_estado($id)
    {
        $sql = "UPDATE usuario SET estado_usuario = 0
            WHERE id_usuario = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => $id
        ));
        $this->objetos = $query->fetchall();
        echo "actualizado";
    }

    function eliminar($id)
    {
        $sql = "DELETE FROM usuario
                WHERE id_usuario=:id
        ";
        $query = $this->acceso->prepare($sql);
        if (!empty($query->execute(array(':id' => $id)))) {
            echo 'delete';
        } else {
            echo 'noDelete';
        }
    }
}
