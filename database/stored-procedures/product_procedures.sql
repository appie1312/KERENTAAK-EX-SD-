DROP PROCEDURE IF EXISTS sp_producten_overzicht;
DROP PROCEDURE IF EXISTS sp_product_zoeken;
DROP PROCEDURE IF EXISTS sp_product_toevoegen;
DROP PROCEDURE IF EXISTS sp_product_wijzigen;
DROP PROCEDURE IF EXISTS sp_product_verwijderen;

DELIMITER //

CREATE PROCEDURE sp_producten_overzicht()
BEGIN
    SELECT id, naam, barcode, prijs, voorraad, houdbaarheidsdatum, omschrijving, status
    FROM products
    WHERE is_actief = 1
    ORDER BY naam;
END //

CREATE PROCEDURE sp_product_zoeken(IN p_id BIGINT UNSIGNED)
BEGIN
    SELECT id, naam, barcode, prijs, voorraad, houdbaarheidsdatum, omschrijving, opmerking
    FROM products
    WHERE id = p_id
    LIMIT 1;
END //

CREATE PROCEDURE sp_product_toevoegen(
    IN p_naam VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_barcode VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_prijs DECIMAL(10,2),
    IN p_voorraad INT,
    IN p_houdbaarheidsdatum DATE,
    IN p_omschrijving VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_opmerking VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_categorie_id BIGINT UNSIGNED;
    DECLARE v_leverancier_id BIGINT UNSIGNED;

    IF EXISTS (SELECT 1 FROM products WHERE naam = p_naam OR barcode = p_barcode) THEN
        SELECT 0 AS gelukt;
    ELSE
        SELECT id INTO v_categorie_id FROM categories WHERE naam = 'Algemeen' LIMIT 1;
        IF v_categorie_id IS NULL THEN
            INSERT INTO categories (naam, omschrijving, datum_aangemaakt)
            VALUES ('Algemeen', 'Standaardcategorie voor producten', CURRENT_TIMESTAMP(6));
            SET v_categorie_id = LAST_INSERT_ID();
        END IF;

        SELECT id INTO v_leverancier_id FROM leveranciers WHERE naam = 'Kniploket Tiko' LIMIT 1;
        IF v_leverancier_id IS NULL THEN
            INSERT INTO leveranciers (naam, contactpersoon, email, datum_aangemaakt)
            VALUES ('Kniploket Tiko', 'Lisa Jansen', 'info@kniplokettiko.nl', CURRENT_TIMESTAMP(6));
            SET v_leverancier_id = LAST_INSERT_ID();
        END IF;

        INSERT INTO products (
            categorie_id, leverancier_id, naam, barcode, prijs, voorraad,
            houdbaarheidsdatum, omschrijving, status, is_actief, opmerking, datum_aangemaakt
        )
        VALUES (
            v_categorie_id, v_leverancier_id, p_naam, p_barcode, p_prijs, p_voorraad,
            p_houdbaarheidsdatum, p_omschrijving, 'Beschikbaar', 1, p_opmerking, CURRENT_TIMESTAMP(6)
        );

        SELECT 1 AS gelukt;
    END IF;
END //

CREATE PROCEDURE sp_product_wijzigen(
    IN p_id BIGINT UNSIGNED,
    IN p_naam VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_barcode VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_prijs DECIMAL(10,2),
    IN p_voorraad INT,
    IN p_houdbaarheidsdatum DATE,
    IN p_omschrijving VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_opmerking VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    IF EXISTS (SELECT 1 FROM products WHERE barcode = p_barcode AND id <> p_id) THEN
        SELECT 0 AS gelukt;
    ELSE
        UPDATE products
        SET
            naam = p_naam,
            barcode = p_barcode,
            prijs = p_prijs,
            voorraad = p_voorraad,
            houdbaarheidsdatum = p_houdbaarheidsdatum,
            omschrijving = p_omschrijving,
            opmerking = p_opmerking,
            datum_gewijzigd = CURRENT_TIMESTAMP(6)
        WHERE id = p_id
        AND NOT (
            naam <=> p_naam
            AND barcode <=> p_barcode
            AND prijs <=> p_prijs
            AND voorraad <=> p_voorraad
            AND houdbaarheidsdatum <=> p_houdbaarheidsdatum
            AND omschrijving <=> p_omschrijving
            AND opmerking <=> p_opmerking
        );

        SELECT IF(ROW_COUNT() > 0, 1, 0) AS gelukt;
    END IF;
END //

CREATE PROCEDURE sp_product_verwijderen(IN p_id BIGINT UNSIGNED)
BEGIN
    IF EXISTS (SELECT 1 FROM products WHERE id = p_id AND is_actief = 0) THEN
        SELECT 0 AS gelukt;
    ELSE
        UPDATE products
        SET is_actief = 0, status = 'Niet beschikbaar', datum_gewijzigd = CURRENT_TIMESTAMP(6)
        WHERE id = p_id AND is_actief = 1;

        SELECT IF(ROW_COUNT() > 0, 1, 0) AS gelukt;
    END IF;
END //

DELIMITER ;
