# Docker Instructies

## Vereisten
- Docker Desktop geïnstalleerd en gestart
- Minimaal 4GB RAM beschikbaar voor Docker

## Snel Starten

### 1. Start de containers
```bash
docker-compose up -d
```

### 2. Wacht op MySQL (±30 seconden)
```bash
docker-compose logs -f mysql
# Wacht tot je ziet: "ready for connections"
# Druk Ctrl+C om te stoppen met logs bekijken
```

### 3. Open de applicatie
- **Website:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
  - Server: mysql
  - Gebruiker: root
  - Wachtwoord: root

## Handige Commando's

| Commando | Beschrijving |
|----------|--------------|
| `docker-compose up -d` | Start alle containers |
| `docker-compose down` | Stop alle containers |
| `docker-compose down -v` | Stop + verwijder database |
| `docker-compose logs -f php` | Bekijk PHP logs |
| `docker-compose logs -f mysql` | Bekijk MySQL logs |
| `docker-compose restart php` | Herstart PHP container |

## Problemen Oplossen

### "Connection refused" fout
```bash
# Wacht langer op MySQL, of herstart:
docker-compose restart
```

### Database resetten
```bash
docker-compose down -v
docker-compose up -d
```

### Bestanden niet bijgewerkt
De bestanden worden automatisch gesynchroniseerd.
Ververs de browser met Ctrl+F5.

## Stoppen

```bash
docker-compose down
```

Of met database verwijderen:
```bash
docker-compose down -v
```
