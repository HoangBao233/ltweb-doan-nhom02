<?php

declare(strict_types=1);

use App\Data\KhoGiaoAn;
use App\Models\GiaoAn;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../inc/ham.php';
require_once __DIR__ . '/../inc/csrf.php';
require_once __DIR__ . '/../inc/kiem-tra.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/xac-thuc.php';

/**
 * UnitTest — Bộ kiểm thử đơn vị PHPUnit cho các hàm bảo mật, xác thực và kiểm tra đầu vào.
 */
final class UnitTest extends TestCase
{
    private array $backupSession = [];
    private array $backupPost = [];
    private array $backupServer = [];

    public static function setUpBeforeClass(): void
    {
        $goc = dirname(__DIR__);
        if (file_exists($goc . '/.env')) {
            Dotenv::createImmutable($goc)->safeLoad();
        }
    }

    protected function setUp(): void
    {
        $this->backupSession = $_SESSION ?? [];
        $this->backupPost    = $_POST;
        $this->backupServer  = $_SERVER;

        $_SESSION = [];
        $_POST    = [];
        unset($_SERVER['HTTP_X_CSRF_TOKEN']);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->backupSession;
        $_POST    = $this->backupPost;
        $_SERVER  = $this->backupServer;
    }

    public function testChuoiChongThamSoMang(): void
    {
        $this->assertSame('python', chuoi('python'));
        $this->assertSame('', chuoi(''));
        $this->assertSame('', chuoi(['ao', 'python']));
        $this->assertSame('', chuoi(null));
        $this->assertSame('', chuoi(123));
    }

    public function testEscapeHtmlChongXss(): void
    {
        $this->assertSame('&lt;b&gt;Bình&lt;/b&gt;', e('<b>Bình</b>'));
        $this->assertSame(
            'Kiểm thử &quot; &#039; &amp;',
            e('Kiểm thử " \' &')
        );
        $this->assertSame('123', e(123));
    }

    public function testDinhDangVndVaAnhGiaoAn(): void
    {
        $this->assertSame('Miễn phí', vnd(0));
        $this->assertSame('150.000 ₫', vnd(150000));
        $this->assertSame('images/banner-trang-chu.jpg', anhGiaoAn(null));
        $this->assertSame('../../uploads/anh.jpg', anhGiaoAn('uploads/anh.jpg', '../../'));
        $this->assertSame('uploads/bai-giang.jpg', anhGiaoAn('bai-giang.jpg'));
    }

    public function testCsrfTokenVaCsrfFieldTrongPhien(): void
    {
        $token1 = csrfToken();
        $token2 = csrfToken();

        $this->assertSame(64, strlen($token1));
        $this->assertTrue(ctype_xdigit($token1));
        $this->assertSame($token1, $token2);

        $field = csrfField();
        $this->assertStringContainsString('type="hidden"', $field);
        $this->assertStringContainsString('name="csrf"', $field);
        $this->assertStringContainsString($token1, $field);
    }

    public function testCsrfHopLeQuaPostVaHeaderFetch(): void
    {
        $token = csrfToken();

        $_POST['csrf'] = $token;
        $this->assertTrue(csrfHopLe());

        $_POST = [];
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $token;
        $this->assertTrue(csrfHopLe());
    }

    public function testCsrfTuChoiMaThieuSaiVaDangMang(): void
    {
        csrfToken();

        $this->assertFalse(csrfHopLe());

        $_POST['csrf'] = 'ma-gia-mao';
        $this->assertFalse(csrfHopLe());

        // Ca 2c: gửi csrf[]=abc không được phép văng TypeError trong hash_equals()
        $_POST['csrf'] = ['abc'];
        $this->assertFalse(csrfHopLe());

        $_POST = [];
        $_SERVER['HTTP_X_CSRF_TOKEN'] = ['abc'];
        $this->assertFalse(csrfHopLe());
    }

    public function testKiemTraGiaoAnHopLe(): void
    {
        [$du, $loi] = kiemTraGiaoAn([
            'ten'         => '  Giáo án Python cơ bản  ',
            'danh_muc_id' => '2',
            'cap_hoc'     => 'THPT',
            'gia'         => '50000',
            'ton_kho'     => '10',
            'video_url'   => '',
            'mo_ta'       => 'Nội dung hợp lệ',
        ], [1, 2, 3]);

        $this->assertSame([], $loi);
        $this->assertSame('Giáo án Python cơ bản', $du['ten']);
        $this->assertSame(2, $du['danh_muc_id']);
        $this->assertSame(50000, $du['gia']);
        $this->assertSame(10, $du['ton_kho']);
    }

    public function testKiemTraGiaoAnBatLoiMangVaSaiWhitelist(): void
    {
        [$du, $loi] = kiemTraGiaoAn([
            'ten'         => ['mang-doc'],
            'danh_muc_id' => '999',
            'cap_hoc'     => 'Đại học',
            'gia'         => '-1000',
            'ton_kho'     => '10000',
            'mo_ta'       => ['m'],
        ], [1, 2, 3]);

        $this->assertSame('', $du['ten']);
        $this->assertArrayHasKey('ten', $loi);
        $this->assertArrayHasKey('danh_muc_id', $loi);
        $this->assertArrayHasKey('cap_hoc', $loi);
        $this->assertArrayHasKey('gia', $loi);
        $this->assertArrayHasKey('ton_kho', $loi);
    }

    public function testCauHinhEnvVaDotenvRequired(): void
    {
        $goc = dirname(__DIR__);
        $this->assertFileExists($goc . '/.env.example');

        $tepEnv = file_exists($goc . '/.env') ? '.env' : '.env.example';
        $env = Dotenv::createImmutable($goc, $tepEnv);
        $env->load();
        $env->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);

        $this->assertArrayHasKey('DB_HOST', $_ENV);
        $this->assertArrayHasKey('DB_DATABASE', $_ENV);
        $this->assertArrayHasKey('DB_USERNAME', $_ENV);
        $this->assertIsBool(($_ENV['APP_DEBUG'] ?? '') === 'true');
    }

    public function testBamMatKhauVaTrangThaiChuaDangNhap(): void
    {
        $bam = password_hash('matkhau123', PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('matkhau123', $bam));
        $this->assertFalse(password_verify('sai-mat-khau', $bam));

        $this->assertNull(nguoiDung());
        $this->assertFalse(daDangNhap());
    }

    public function testPdoTatEmulatePrepares(): void
    {
        $pdo = db();
        $this->assertInstanceOf(PDO::class, $pdo);
        $this->assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE));
        $this->assertFalse((bool) $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES));
    }

    public function testTaiKhoanSeedVaGioiHanBruteForceDangNhap(): void
    {
        $pdo = db();
        $st  = $pdo->prepare('SELECT email, mat_khau, vai_tro FROM nguoi_dung WHERE email = ? LIMIT 1');
        $st->execute(['admin@ued.vn']);
        $admin = $st->fetch();

        $this->assertIsArray($admin);
        $this->assertSame('quan_tri', $admin['vai_tro']);
        $this->assertTrue(password_verify('matkhau123', $admin['mat_khau']));

        $emailThu = 'unittest_brute_' . bin2hex(random_bytes(4)) . '@ued.vn';
        $logPath  = __DIR__ . '/../logs/bao-mat.log';
        $logTruoc = file_exists($logPath) ? file_get_contents($logPath) : null;

        $pdo->beginTransaction();
        try {
            $this->assertSame(0, soLanSai($emailThu));
            for ($i = 0; $i < 5; $i++) {
                ghiLanSai($emailThu);
            }
            $this->assertGreaterThanOrEqual(5, soLanSai($emailThu));
        } finally {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($logTruoc === null) {
                if (file_exists($logPath)) {
                    unlink($logPath);
                }
            } else {
                file_put_contents($logPath, $logTruoc);
            }
        }
    }

    public function testGhiLogBaoMatKhongLoMatKhauHoacCsrf(): void
    {
        $logPath  = __DIR__ . '/../logs/bao-mat.log';
        $logTruoc = file_exists($logPath) ? file_get_contents($logPath) : null;

        try {
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            $_SESSION['uid']        = 4;

            ghiLogBaoMat('kiem_thu_unit', ['trang' => '/unit-test']);
            $this->assertFileExists($logPath);

            $noiDung = (string) file_get_contents($logPath);
            $this->assertStringContainsString('kiem_thu_unit', $noiDung);
            $this->assertStringContainsString('"ip":"127.0.0.1"', $noiDung);
            $this->assertStringContainsString('"uid":4', $noiDung);
            $this->assertStringNotContainsString('matkhau123', $noiDung);
        } finally {
            if ($logTruoc === null) {
                if (file_exists($logPath)) {
                    unlink($logPath);
                }
            } else {
                file_put_contents($logPath, $logTruoc);
            }
        }
    }

    public function testKhoGiaoAnTruyVanAnToan(): void
    {
        $kho = new KhoGiaoAn();
        $this->assertSame([], $kho->theoDanhSachId([]));

        $ds = $kho->theoDanhSachId([1]);
        $this->assertNotEmpty($ds);
        $this->assertInstanceOf(GiaoAn::class, $ds[0]);
        $this->assertSame(1, $ds[0]->id);
    }
}
