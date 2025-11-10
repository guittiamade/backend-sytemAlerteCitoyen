param(
  [int]$Port = 8000
)

function Get-LocalIPv4 {
  try {
    $ip = Get-NetIPAddress -AddressFamily IPv4 |
      Where-Object { $_.IPAddress -notmatch '^169\.254' -and $_.IPAddress -ne '127.0.0.1' -and $_.InterfaceOperationalStatus -eq 'Up' } |
      Sort-Object -Property InterfaceMetric, SkipAsSource |
      Select-Object -ExpandProperty IPAddress -First 1
    if ($ip) { return $ip }
  } catch {}
  # Fallback parsing (ipconfig)
  $lines = ipconfig | Out-String
  $m = [regex]::Matches($lines, 'IPv4 Address[ .]*: *(\d+\.\d+\.\d+\.\d+)')
  if ($m.Count -gt 0) { return $m[0].Groups[1].Value }
  return '127.0.0.1'
}

# Validate PHP availability
try {
  $phpVersion = & php -v 2>$null
} catch {
  Write-Error 'PHP is not available in PATH. Please install PHP and ensure `php` is accessible.'
  exit 1
}

# Ensure artisan exists
if (-not (Test-Path -LiteralPath './artisan')) {
  Write-Error 'artisan not found. Please run this script from the project root.'
  exit 1
}

$ip = Get-LocalIPv4
Write-Host "Starting Laravel dev server on 0.0.0.0:$Port ..." -ForegroundColor Cyan
Write-Host "Accessible from this machine:    http://localhost:$Port" -ForegroundColor Green
Write-Host "Accessible on local network:    http://$ip:$Port" -ForegroundColor Green

# Optional tip for .env
if (-not (Test-Path -LiteralPath './.env')) {
  Write-Warning 'No .env found. Consider copying .env.example to .env and running migrations.'
}

# Start the server (blocking)
php artisan serve --host=0.0.0.0 --port=$Port
