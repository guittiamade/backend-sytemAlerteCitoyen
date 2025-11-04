<!DOCTYPE html>
<html lang="fr">
<body>
  <p>{{ $messageBody }}</p>
  @if($alerteId)
    <p>ID du signalement: <strong>{{ $alerteId }}</strong></p>
  @endif
  <p>— Système Alerte Citoyen</p>
</body>
</html>


