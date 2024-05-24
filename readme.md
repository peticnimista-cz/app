# Technowizz 2023
- Technologický stack: **PHP 8.2 (Nette 3.0+), MariaDB**
- API Testování: **Postman, manuální testování**
- Používané requesty: **GET - získávání dat, POST - nahrávání dat, PATCH - celá i částečná úprava dat, DELETE - odstranění dat**

API pro aplikaci sloužicí jako náhrada za stávajicí tabulku materiálů a projektů (vč. přípravků jako lepidla, ředidla apod.) 
vč. řešení uživatelské autentizace a autorizace.

## Datová struktura
Struktura databáze je tvořena základními logickými celky:
- Datová část (data)
  - material
  - projekt
  - redidlo
  - lepidlo
  - kluzny_lak
  - granulat
  - cistic
- Dynamická část (dyn)
  - view_note - Poznámka k specifické stránce či listu
  - row_note - Poznámka k jakékoliv datové buňce či hodnotě
- Systémová část (sys)
  - user - Uživatele aplikace
  - history - Historie veškerých změn v aplikaci
  - accesslog - Přístupový záznamník jednotlivých uživatelů

## Debugmode
V případě zapnutého debug módu budou serverové chyby (HTTP 500 - serverová či databázová chyba, 404 - nenalezení endpointu) zachycené chytrou laděnkou Tracy 
v opačném případě budou zachyceny jako bežný endpoint s patřičnou odpovědí (v tomto případě odpověď se statusem error).

### Testování
Při zapnutém debug módu API nabývá o několik testovacích enpointů.
- `api/test/sitemap` - zobrazení veškerých API routů vč. jejich parametrů a povolených requestů
- `api/test/post` - vrací zaslanou hlavičku
- `api/test/password` - generuje hash hesla "kolokolo" (testovací heslo)
- `api/test` - základní testovací endpoint, vypisuje HTTP kód a request/response

Nastavení debug módu je uloženo v konstantě `Bootstrap::DEBUG_MODE` (v základu FALSE).

## Autentizace
Autentizace je spojena s entitou uživatele v databázi (uživatel se přihlašuje) a je řešena serverově (spojení s klientem je uchováno na straně server - klient nemusí nic uchovávat). 
Do systému se přihlašujeme pomocí emailu a hesla, viz níže příkladný request. 
```
POST api/system/user/login

{
    "email": "john.doe@example.com",
    "password": "password"
}
```
Pokud je přihlášení platné, server vrací kód 200. V případě neplatné kombinace přístupových údajů vrací server chybu s kódem 401. 
Informace o přihlášeném uživatele zkontrolujeme na
```
GET api/system/user/info 200

{
    ...
    "content": {
        "id": <ID:int>
        "logged": true|false
    }
    ...
}
```

## Autorizace
Autorizace hovoří mezi vztahem jednotlivých uživatelů a logických celků aplikace. 
Každý uživatel má tedy svá práva (nodes), která mu byla přidělena. 

Samotné oprávnění jsou v systému velmi jednoduše zařízené: 
- každý logický celek dle databáze (např. data -> projekt) obsahuje právo na **čtení (READ)** a **právo na úpravu (EDIT)**.
  ```
    const DATA_MATERIAL_EDIT = "data.material.edit";
    const DATA_MATERIAL_READ = "data.material.read";
    const DATA_PROJEKT_READ = "data.projekt.read";
    const DATA_PROJEKT_EDIT = "data.projekt.edit";

    const DATA_CISTIC_EDIT = "data.cistic.edit";
    const DATA_CISTIC_READ = "data.cistic.read";

    const DATA_REDIDLO_EDIT = "data.redidlo.edit";
    const DATA_REDIDLO_READ = "data.redidlo.read";

    const DATA_LEPIDLO_EDIT = "data.lepidlo.edit";
    const DATA_LEPIDLO_READ = "data.lepidlo.read";

    const DATA_GRANULAT_EDIT = "data.granulat.edit";
    const DATA_GRANULAT_READ = "data.granulat.read";

    const DATA_KLUZNY_LAK_EDIT = "data.kluzny_lak.edit";
    const DATA_KLUZNY_LAK_READ = "data.kluzny_lak.read";  
  ```
- celková správa uživatelů je opatřena právem: `system.user.management`
- psaní globálně viditelných poznámek: `data.global_notes` (budou viditelné ostatními uživateli)
- superuser/plná práva: `*`

## Logování akcí a historie změn
Každá akce provedená v aplikaci uživatlelem bude zalogována v sys > history. 
U každé akce je uchován autor, typ změny (GET/POST/PATCH/DELETE), úpravy (minulá verze a nová verze) a datum změny.