<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Justificar Ausencia - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 700px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { min-height: 100px; resize: vertical; }
        button { padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Justificar Ausencia</h1>
        <div>
            <a href="?action=my_justifications">← Mis Justificaciones</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="info">
                📌 Complete el formulario para justificar una ausencia. Puede adjuntar documentos de respaldo (certificados médicos, etc.)
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Motivo de la Ausencia *</label>
                    <textarea name="reason" required placeholder="Describa el motivo de la ausencia..."></textarea>
                </div>

                <div class="form-group">
                    <label>Documento de Respaldo (opcional)</label>
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Formatos permitidos: PDF, JPG, PNG - Máximo 5MB
                    </small>
                </div>

                <button type="submit">Enviar Justificación</button>
            </form>
        </div>
    </div>
</body>
</html>