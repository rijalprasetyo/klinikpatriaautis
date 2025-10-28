<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alamat')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('alamat')->nullable();
            $table->string('password');
            $table->string('no_hp')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokter');
            $table->string('email')->unique();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('status')->default(1); // ubah ke boolean default 1
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp');
            $table->timestamp('expires_at');
            $table->timestamps();
        });



        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });


        Schema::create('kategori_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        Schema::create('data_backup', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');      // nama file zip hasil backup
            $table->string('file_path');      // lokasi file disimpan
            $table->string('file_size')->nullable(); // ukuran file (byte)
            $table->string('created_by')->nullable(); // siapa yang melakukan backup
            $table->string('status')->default('success'); // status backup
            $table->timestamps();
        });


        DB::table('kategori_pendaftaran')->insert([
            ['nama_kategori' => 'Disabilitas (Dengan SKTM)'],
            ['nama_kategori' => 'Disabilitas (Non-SKTM)'],
            ['nama_kategori' => 'Masyarakat Umum'],
        ]);

        Schema::create('jam_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->timestamps();
        });

        DB::table('jam_pelayanan')->insert([
            ['jam_mulai' => '07:00', 'jam_selesai' => '08:00'],
            ['jam_mulai' => '08:00', 'jam_selesai' => '09:00'],
            ['jam_mulai' => '09:00', 'jam_selesai' => '10:00'],
            ['jam_mulai' => '10:00', 'jam_selesai' => '11:00'],
            ['jam_mulai' => '11:00', 'jam_selesai' => '12:00'],
            ['jam_mulai' => '16:00', 'jam_selesai' => '17:00'],
            ['jam_mulai' => '17:00', 'jam_selesai' => '18:00'],
            ['jam_mulai' => '18:00', 'jam_selesai' => '19:00'],
            ['jam_mulai' => '19:00', 'jam_selesai' => '20:00'],
        ]);

        Schema::create('jenis_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('pelayanan');
            $table->string('icon_pelayanan')->nullable();
            $table->timestamps();
        });

        DB::table('jenis_pelayanan')->insert([
            ['pelayanan' => 'Non-Disabilitas', 'icon_pelayanan' => 'Non-Disabilitas.jpg'],
            ['pelayanan' => 'Autism Spectrum Disorder', 'icon_pelayanan' => 'Autism.jpg'],
            ['pelayanan' => 'Cerebral Palsy', 'icon_pelayanan' => 'Cerebral.jpg'],
            ['pelayanan' => 'Down Syndrome', 'icon_pelayanan' => 'down.jpg'],
            ['pelayanan' => 'Cedera Anak-Dewasa', 'icon_pelayanan' => 'Faktor.jpg'],
            ['pelayanan' => 'Gangguan Postural', 'icon_pelayanan' => 'Gangguan.jpg'],
            ['pelayanan' => 'Gross Delay Development', 'icon_pelayanan' => 'Gross.jpg'],
            ['pelayanan' => 'Gangguan Sensorik & Motorik', 'icon_pelayanan' => 'Input.jpg'],
            ['pelayanan' => 'Layanan Pasca Operasi', 'icon_pelayanan' => 'Layanan.jpg'],
        ]);


        Schema::create('data_pasien', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian');
            $table->string('nama_pasien');
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nomor_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pendamping')->nullable();
            $table->string('kategori_pendaftaran')->nullable();
            $table->foreignId('layanan_id')->constrained('jenis_pelayanan')->onDelete('cascade');
            $table->foreignId('waktu_id')->constrained('jam_pelayanan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('keluhan')->nullable();
            $table->date('tgl_kunjungan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('sktm')->nullable();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->onDelete('set null');
            $table->string('status_pemeriksaan')->nullable();
            $table->string('status_berkas')->nullable();
            $table->text('catatan_pemeriksaan')->nullable();
            $table->text('catatan_obat')->nullable();
            $table->string('video_before')->nullable();
            $table->string('video_after')->nullable();
            $table->string('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pasien');
        Schema::dropIfExists('jenis_pelayanan');
        Schema::dropIfExists('jam_pelayanan');
        Schema::dropIfExists('kategori_pendaftaran');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('dokters');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('users');
    }
};
