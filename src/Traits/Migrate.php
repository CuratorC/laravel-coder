<?php
namespace CuratorC\Coder\Traits;

trait Migrate
{
    public function migratePath()
    {
        // 数据库迁移文件
        $this->createMigrationContent();

        // 若为主表
        if ($this->param['table_type'] == 'main') {
            // 添加模型工厂
            $this->createFactoryContent();
            $this->createSeederContent();
        }

        // 若为衍生表
        if ($this->param['table_type'] == 'derive') {
            // 添加模型工厂
            $this->createMigrationSeedContent();
        }

        // $this->createTinyIntegerMap();
    }

    private function createMigrationContent()
    {
        $text = '<?php';
        // 为业务表添加权限所需引用
        if ($this->param['table_type'] == 'main') {
            $text .= '
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\\' . $this->Model . ';';
        }

        $text .= '
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create' . $this->Models . 'Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\'' . $this->models . '\', function (Blueprint $table) {';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
            $table->integer(\'' . $col['key'] . '\')->default(0)->comment(\'' . $col['name'] . '\');';

                break;
                case 'foreign':
                $text .= '
            $table->integer(\'' . $col['key'] . '_id\')->default(0)->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'integer-index':
                $text .= '
            $table->integer(\'' . $col['key'] . '\')->default(0)->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'integer-unique':
                $text .= '
            $table->integer(\'' . $col['key'] . '\')->default(0)->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'string':
                $text .= '
            $table->string(\'' . $col['key'] . '\')->nullable()->comment(\'' . $col['name'] . '\');';

                break;
                case 'string-index':
                $text .= '
            $table->string(\'' . $col['key'] . '\')->default(\'\')->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'string-unique':
                $text .= '
            $table->string(\'' . $col['key'] . '\')->default(\'\')->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'tinyInteger':
                $text .= '
            $table->tinyInteger(\'' . $col['key'] . '\')->default(' . $this->Model . '::' . strtoupper($col['key']) . '_OPEN_CODE)->index()->comment(\'' . $col['name'] . '\');';

                break;
                case 'text':
                $text .= '
            $table->text(\'' . $col['key'] . '\')->nullable()->comment(\'' . $col['name'] . '\');';

                break;
                case 'image':
                $text .= '
            $table->string(\'' . $col['key'] . '\')->nullable()->comment(\'' . $col['name'] . '\');';

                break;
                case 'decimal':
                $text .= '
            $table->decimal(\'' . $col['key'] . '\', 20, 8)->default(0)->comment(\'' . $col['name'] . '\');';

                break;
                case 'timestamp':
                $text .= '
            $table->timestamp(\'' . $col['key'] . '\')->nullable()->comment(\'' . $col['name'] . '\');';

                break;
                case 'primary':
                $text .= '
            $table->id(\'id\')->comment(\'' . $col['name'] . ' ID\');';

                break;
                default:
            }
        }

        // 补充默认追加字段

        if (in_array('status', $this->defaultCols)) {
            $text .= '
            $table->tinyInteger(\'status\')->default(' . $this->Model . '::STATUS_OPEN_CODE)->index()->comment(\'状态\');';
        }
        if (in_array('admin_remark', $this->defaultCols)) {
            $text .= '
            $table->text(\'admin_remark\')->nullable()->comment(\'管理员备注\');';
        }
        if (in_array('timestamps', $this->defaultCols)) {
            $text .= '
            $table->timestamps();';
        }
        if (in_array('updated_by', $this->defaultCols)) {
            $text .= '
            $table->integer(\'updated_by\')->default(0)->comment(\'更新者\');';
        }
        if (in_array('deleted_at', $this->defaultCols)) {
            $text .= '
            $table->timestamp(\'deleted_at\')->nullable()->comment(\'删除时间\');';
        }
        $text .= '
        });';
        if ($this->param['table_type'] == 'main') {
            $text .= '

        // 添加初始权限
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // ' . $this->param['object_name'] . '权限
        Permission::create([\'name\' => \'' . $this->param['object_name'] . '-查看权限\']);
        Permission::create([\'name\' => \'' . $this->param['object_name'] . '-新增权限\']);
        Permission::create([\'name\' => \'' . $this->param['object_name'] . '-修改权限\']);
        Permission::create([\'name\' => \'' . $this->param['object_name'] . '-删除权限\']);

        // 给超级管理员赋予权限
        $admin = Role::find(1);
        $admin->givePermissionTo(
            \'' . $this->param['object_name'] . '-查看权限\',
            \'' . $this->param['object_name'] . '-新增权限\',
            \'' . $this->param['object_name'] . '-修改权限\',
            \'' . $this->param['object_name'] . '-删除权限\'
        );
        ';
        }
        $text .= '
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\'' . $this->models . '\');
    }
}
';
        $this->writeToFile(
            app_path() . '/../database/migrations/' . date('Y_m_d_His') . '_create_' . $this->models . '_table.php',
            $text
        );

    }


    private function createMigrationSeedContent()
    {
        $text = '<?php

use Illuminate\Database\Migrations\Migration;
use Faker\Generator as Faker;

class Seed' . $this->Models . 'Data extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $' . $this->models . ' = [
            [
                \'name\'     => \'名称\',
            ],
        ];

        DB::table(\'' . $this->models . '\')->insert($' . $this->models . ');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 回滚数据
        DB::table(\'' . $this->models . '\')->truncate();
    }
}
';
        $this->writeToFile(
            app_path() . '/../database/migrations/' . date('Y_m_d_His', time() + 1) . '_seed_' . $this->models . '_data.php',
            $text
        );
    }

    private function createFactoryContent()
    {
        $text = '<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\\' . $this->Model . ';
use Illuminate\Database\Eloquent\Factories\Factory;

class ' . $this->Model . 'Factory extends Factory
{
    /**
     * The name of the factory\'s corresponding model.
     *
     * @var string
    */
    protected $model = ' . $this->Model . '::class;


    /**
     * Define the model\'s default state.
     *
     * @return array
     */
    public function definition()
    {
        return [';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'integer':
                $text .= '
            \'' . $col['key'] . '\' => rand(1, 50),';
                break;
                case 'foreign':
                $text .= '
            \'' . $col['key'] . '_id\' => rand(1, 50),';
                break;
                case 'integer-index':
                $text .= '
            \'' . $col['key'] . '\' => rand(1, 50),';
                break;
                case 'integer-unique':
                $text .= '
            \'' . $col['key'] . '\' => rand(1, 50),';
                break;
                case 'string':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->name,';
                break;
                case 'string-index':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->name,';
                break;
                case 'string-unique':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->name,';
                break;
                case 'tinyInteger':
                $text .= '
            \'' . $col['key'] . '\' => rand(0, 1)?1:9,';
                break;
                case 'text':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->sentence(),';
                break;
                case 'image':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->sentence(),';
                break;
                case 'decimal':
                $text .= '
            \'' . $col['key'] . '\' => rand(1, 50),';
                break;
                case 'timestamp':
                $text .= '
            \'' . $col['key'] . '\' => $this->faker->dateTimeThisMonth(),';
                break;
                case 'primary':
                break;
                default:
            }
        }
        $text .= '
        ];
    }
}
';
    $this->writeToFile(app_path() . '/../database/factories/' . $this->Model . 'Factory.php', $text);
    }


    /**
     * 创建初始数据迁移文件
     * @return           [type] [description]
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-30)
     */
    private function createSeederContent()
    {
        $text = '<?php

use App\Models\\' . $this->Model . ';
use Illuminate\Database\Seeder;

class ' . $this->Models . 'Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取faker实例
        // $faker = app(Faker\Generator::class);

        //生成数据集合
        $' . $this->models . ' = factory(' . $this->Model . '::class)
            ->times(10)
            ->make()

            ->each(function ($' . $this->model . ', $index)
            //    use ($faker)
            {
                // 每条数据的修改操作
            })
            // ->makeVisible([\'password\']) // 隐藏字段临时可修改
            ->toArray();
        //让隐藏字段可见，并将数据集合转换为数组

        // 插入到数据库中
        ' . $this->Model . '::insert($' . $this->models . ');
    }
}
';
        $this->writeToFile(app_path() . '/../database/seeders/' . $this->Models . 'Seeder.php', $text);

        // 注册
        $this->registerFile(
            app_path() . '/../database/seeders/DatabaseSeeder.php',
            '
        // ' . $this->param['object_name'] . '
        $this->call(' . $this->Models . 'TableSeeder::class);
        ',
        '//<--模型工厂标记行，请勿删除'
        );
    }

    private function createTinyIntegerMap()
    {
        $text = '
    // ' . $this->param['object_name'] . '
    \'' . $this->models . '\' => [';
        foreach ($this->tableCol as $col) {
            switch ($col['type']) {
                case 'tinyInteger':
                    $text .= '
        // ' . $col['name'] . '
        \'' . $col['key'] . '\'    => [1=>\'正常\', 9=>\'冻结\'],';

                    break;

                default:
            }
        }

        if (in_array('status', $this->defaultCols)) {
            $text .= '
        // 状态
        \'status\'    => [1=>\'正常\', 9=>\'冻结\'],';

        }
        $text .= '
    ],';

        // 加入config
        $this->registerFile(
            app_path() . '/../config/tinyIntegerMap.php',
            $text,
            '//<--短码索引标记，请勿删除'
        );
    }
}