<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_property_detail_page(): void
    {
        $user = User::factory()->create();

        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);

        $response = $this->actingAs($user)->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertSee('Rumah Utama');
        $response->assertSee('Aset');
    }

    public function test_user_can_store_a_new_asset_for_property(): void
    {
        $user = User::factory()->create();

        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);

        $response = $this->actingAs($user)->post(route('properties.assets.store', $property), [
            'name' => 'AC Living Room',
            'category' => 'Elektronik',
            'purchase_date' => '2026-08-01',
            'purchase_price' => '3500000',
            'condition' => 'Normal',
            'description' => 'Pendingin ruang tamu',
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('assets', [
            'property_id' => $property->id,
            'name' => 'AC Living Room',
            'condition' => 'Normal',
        ]);
    }

    public function test_user_can_store_maintenance_log_for_asset(): void
    {
        $user = User::factory()->create();

        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);

        $category = Category::create(['name' => 'Elektronik']);

        $asset = Asset::create([
            'property_id' => $property->id,
            'category_id' => $category->id,
            'name' => 'AC Living Room',
            'condition' => 'Normal',
            'purchase_price' => 3500000,
            'purchase_date' => '2026-08-01',
            'description' => 'Pendingin ruang tamu',
        ]);

        $response = $this->actingAs($user)->post(route('properties.maintenance_logs.store', $property), [
            'asset_id' => $asset->id,
            'type' => 'Repair',
            'service_date' => '2026-08-20',
            'cost' => '450000',
            'notes' => 'Cuci AC dan isi freon',
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('maintenance_logs', [
            'asset_id' => $asset->id,
            'type' => 'Repair',
            'cost' => '450000.00',
            'notes' => 'Cuci AC dan isi freon',
        ]);
    }

    public function test_user_can_delete_asset_from_property(): void
    {
        $user = User::factory()->create();
        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);
        $category = Category::create(['name' => 'Elektronik']);

        $asset = Asset::create([
            'property_id' => $property->id,
            'category_id' => $category->id,
            'name' => 'Kulkas',
            'condition' => 'Normal',
            'purchase_price' => 2500000,
            'purchase_date' => '2026-08-10',
        ]);

        $response = $this->actingAs($user)->delete(route('properties.assets.destroy', [$property, $asset]));

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }

    public function test_user_can_update_asset_from_property(): void
    {
        $user = User::factory()->create();
        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);
        $category = Category::create(['name' => 'Elektronik']);

        $asset = Asset::create([
            'property_id' => $property->id,
            'category_id' => $category->id,
            'name' => 'Kulkas',
            'condition' => 'Normal',
            'purchase_price' => 2500000,
            'purchase_date' => '2026-08-10',
        ]);

        $response = $this->actingAs($user)->put(route('properties.assets.update', [$property, $asset]), [
            'name' => 'Kulkas Baru',
            'category' => 'Elektronik',
            'condition' => 'Perlu Servis',
            'purchase_price' => '2800000',
            'purchase_date' => '2026-08-12',
            'description' => 'AC mini baru',
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Kulkas Baru',
            'condition' => 'Perlu Servis',
            'purchase_price' => '2800000.00',
        ]);
    }

    public function test_user_can_update_maintenance_log(): void
    {
        $user = User::factory()->create();
        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);
        $category = Category::create(['name' => 'Elektronik']);

        $asset = Asset::create([
            'property_id' => $property->id,
            'category_id' => $category->id,
            'name' => 'AC Living Room',
            'condition' => 'Normal',
            'purchase_price' => 3500000,
            'purchase_date' => '2026-08-01',
        ]);

        $log = $asset->maintenanceLogs()->create([
            'type' => 'Routine Checkup',
            'service_date' => '2026-08-20',
            'cost' => 450000,
            'notes' => 'Cuci AC',
        ]);

        $response = $this->actingAs($user)->put(route('properties.maintenance_logs.update', [$property, $log]), [
            'type' => 'Repair',
            'service_date' => '2026-08-25',
            'cost' => '600000',
            'notes' => 'Cuci AC dan isi freon',
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('maintenance_logs', [
            'id' => $log->id,
            'type' => 'Repair',
            'cost' => '600000.00',
            'notes' => 'Cuci AC dan isi freon',
        ]);
    }

    public function test_user_can_delete_maintenance_log(): void
    {
        $user = User::factory()->create();
        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);
        $category = Category::create(['name' => 'Elektronik']);

        $asset = Asset::create([
            'property_id' => $property->id,
            'category_id' => $category->id,
            'name' => 'AC Living Room',
            'condition' => 'Normal',
            'purchase_price' => 3500000,
            'purchase_date' => '2026-08-01',
        ]);

        $log = $asset->maintenanceLogs()->create([
            'type' => 'Repair',
            'service_date' => '2026-08-20',
            'cost' => 450000,
            'notes' => 'Cuci AC dan isi freon',
        ]);

        $response = $this->actingAs($user)->delete(route('properties.maintenance_logs.destroy', [$property, $log]));

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseMissing('maintenance_logs', ['id' => $log->id]);
    }

    public function test_user_can_update_financial_settings(): void
    {
        $user = User::factory()->create([
            'monthly_salary' => 5000000,
            'maintenance_budget_percentage' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('financials.update'), [
            'monthly_salary' => '7500000',
            'maintenance_budget_percentage' => '15',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'monthly_salary' => '7500000.00',
            'maintenance_budget_percentage' => 15,
        ]);
    }

    public function test_dashboard_shows_financial_summary_and_property_health_score(): void
    {
        $user = User::factory()->create([
            'monthly_salary' => 10000000,
            'maintenance_budget_percentage' => 20,
        ]);

        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);

        $normalAsset = Asset::create([
            'property_id' => $property->id,
            'name' => 'AC',
            'condition' => 'Normal',
            'purchase_price' => 2500000,
            'purchase_date' => '2026-08-01',
        ]);

        $warnAsset = Asset::create([
            'property_id' => $property->id,
            'name' => 'Mesin Pompa',
            'condition' => 'Perlu Servis',
            'purchase_price' => 1500000,
            'purchase_date' => '2026-08-02',
        ]);

        $normalAsset->maintenanceLogs()->create([
            'type' => 'Routine Checkup',
            'service_date' => now()->toDateString(),
            'cost' => 500000,
            'notes' => 'Servis ringan',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Aman');
        $response->assertSee('Health Score');
        $response->assertSee('50%');
    }

    public function test_user_can_export_property_report_pdf_view(): void
    {
        $user = User::factory()->create();
        $property = Property::create([
            'user_id' => $user->id,
            'name' => 'Rumah Utama',
            'type' => 'Home',
            'address' => 'Jl. Merdeka No. 12',
        ]);

        $response = $this->actingAs($user)->get(route('properties.export-pdf', $property));

        $response->assertOk();
        $response->assertSee('Laporan Properti');
    }
}
