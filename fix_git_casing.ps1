# Fix Git Folder Capitalization for PSR-4 Autoloading
$folders = @("models", "controllers", "views", "routes", "migrations", "database")
$basePath = "app/Modules/PenilaianDanPresensi"

cd d:\almahir

foreach ($folder in $folders) {
    $lower = "$basePath/$folder"
    $upper = "$basePath/$($folder.Substring(0,1).ToUpper())$($folder.Substring(1))"
    
    # Check if tracked in git as lowercase
    $isTracked = git ls-files $lower
    if ($isTracked) {
        Write-Host "Fixing casing for $lower -> $upper"
        git mv $lower "${lower}_tmp"
        git mv "${lower}_tmp" $upper
    }
}

git commit -m "fix(namespace): resolve case-sensitivity issue for PSR-4 on Linux production server"
git push
