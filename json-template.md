# Template des formats de donnée que l'API doit envoyer au Front

```json
{
    "id": 53,
    "date": "2022-10-09T11:25:29+02:00",
    "arena": {
        "id": 33,
        "name": "Rey",
        "address": "8 Avenue des Gayeulles, 35700 Rennes",
        "zipCode": 17348,
        "longitude": 4.9,
        "latitude": 44.9333
    },
    "type": {
        "id": 18,
        "name": "match aller"
    },
    "teams": [
        {
            "id": 32,
            "name": "Les Cerfs N3",
            "club": {
                "id": 22,
                "name": "Les Cerfs",
                "address": "Rue Charles de Gaulle, 38760 VARCES",
                "longitude": 2.3744378,
                "latitude": 48.8356758
            },
            "category": {
                "id": 17,
                "name": "N3"
            }
        },
        {
            "id": 38,
            "name": "Krokos N3",
            "club": {
                "id": 30,
                "name": "Krokos",
                "address": "11 Rue Colette, 67200 Strasbourg",
                "longitude": 7.6991209,
                "latitude": 48.5866336
            },
            "category": {
                "id": 15,
                "name": "N3"
            }
        }
    ],
    "users": [
        {
            "id": 44,
            "firstname": "Claude",
            "email": "royer.patricia@yahoo.fr",
            "level": "D2",
            "longitude": 4.9,
            "latitude": 44.9333,
            "phoneNumber": "+33 (0)6 64 62 47 41"
        },
        {
            "id": 56,
            "firstname": "Denise",
            "email": "omenard@sfr.fr",
            "level": "D2",
            "longitude": 7.6991209,
            "latitude": 48.5866336,
            "phoneNumber": "0752509965"
        }
    ]
}
```
