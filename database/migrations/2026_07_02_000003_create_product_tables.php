<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table): void {
                $table->id();
                $table->string('naam', 50)->unique();
                $table->string('omschrijving')->nullable();
                $table->boolean('is_actief')->default(true);
                $table->string('opmerking')->nullable();
                $table->dateTime('datum_aangemaakt', 6)->useCurrent();
                $table->dateTime('datum_gewijzigd', 6)->nullable()->useCurrentOnUpdate();
            });
        }

        if (! Schema::hasTable('leveranciers')) {
            Schema::create('leveranciers', function (Blueprint $table): void {
                $table->id();
                $table->string('naam', 100);
                $table->string('contactpersoon', 100)->nullable();
                $table->string('telefoonnummer', 20)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('adres')->nullable();
                $table->boolean('is_actief')->default(true);
                $table->string('opmerking')->nullable();
                $table->dateTime('datum_aangemaakt', 6)->useCurrent();
                $table->dateTime('datum_gewijzigd', 6)->nullable()->useCurrentOnUpdate();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete()->cascadeOnUpdate();
                $table->foreignId('leverancier_id')->constrained('leveranciers')->restrictOnDelete()->cascadeOnUpdate();
                $table->string('naam', 150);
                $table->string('barcode', 20)->unique();
                $table->decimal('prijs', 10, 2);
                $table->integer('voorraad')->default(0);
                $table->date('houdbaarheidsdatum')->nullable();
                $table->string('omschrijving')->nullable();
                $table->string('status', 50)->default('Beschikbaar');
                $table->boolean('is_actief')->default(true);
                $table->string('opmerking')->nullable();
                $table->dateTime('datum_aangemaakt', 6)->useCurrent();
                $table->dateTime('datum_gewijzigd', 6)->nullable()->useCurrentOnUpdate();
            });
        }

        DB::table('categories')->updateOrInsert(
            ['naam' => 'Algemeen'],
            [
                'omschrijving' => 'Standaardcategorie voor producten',
                'is_actief' => true,
                'datum_gewijzigd' => now(),
            ],
        );

        DB::table('leveranciers')->updateOrInsert(
            ['naam' => 'Kniploket Tiko'],
            [
                'contactpersoon' => 'Lisa Jansen',
                'email' => 'info@kniplokettiko.nl',
                'is_actief' => true,
                'datum_gewijzigd' => now(),
            ],
        );

        $categoryId = DB::table('categories')->where('naam', 'Algemeen')->value('id');
        $supplierId = DB::table('leveranciers')->where('naam', 'Kniploket Tiko')->value('id');

        DB::table('products')->updateOrInsert(
            ['barcode' => '871000000001'],
            [
                'categorie_id' => $categoryId,
                'leverancier_id' => $supplierId,
                'naam' => 'Repair Shampoo 250ml',
                'prijs' => 12.95,
                'voorraad' => 25,
                'omschrijving' => 'Shampoo voor beschadigd haar',
                'status' => 'Beschikbaar',
                'is_actief' => true,
                'datum_gewijzigd' => now(),
            ],
        );

        DB::table('products')->updateOrInsert(
            ['barcode' => '871000000002'],
            [
                'categorie_id' => $categoryId,
                'leverancier_id' => $supplierId,
                'naam' => 'Matte Wax 100ml',
                'prijs' => 9.95,
                'voorraad' => 40,
                'omschrijving' => 'Wax met matte finish',
                'status' => 'Beschikbaar',
                'is_actief' => true,
                'datum_gewijzigd' => now(),
            ],
        );

        $this->createStoredProcedures();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_producten_overzicht');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_zoeken');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_toevoegen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_wijzigen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_verwijderen');

        Schema::dropIfExists('products');
        Schema::dropIfExists('leveranciers');
        Schema::dropIfExists('categories');
    }

    private function createStoredProcedures(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_producten_overzicht');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_zoeken');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_toevoegen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_wijzigen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_product_verwijderen');

        DB::unprepared("
            CREATE PROCEDURE sp_producten_overzicht()
            BEGIN
                SELECT id, naam, barcode, prijs, voorraad, houdbaarheidsdatum, omschrijving, status
                FROM products
                WHERE is_actief = 1
                ORDER BY naam;
            END
        ");

        DB::unprepared("
            CREATE PROCEDURE sp_product_zoeken(IN p_id BIGINT UNSIGNED)
            BEGIN
                SELECT id, naam, barcode, prijs, voorraad, houdbaarheidsdatum, omschrijving, opmerking
                FROM products
                WHERE id = p_id
                LIMIT 1;
            END
        ");

        DB::unprepared("
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
            END
        ");

        DB::unprepared("
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
            END
        ");

        DB::unprepared("
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
            END
        ");
    }
};
