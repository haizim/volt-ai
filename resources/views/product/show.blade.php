<x-volt-app title="Product Detail">
    <x-volt-panel title="Detail">
        <table class="ui very basic table">
            <tbody>
                <tr>
                    <td><strong>Name</strong></td>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <td><strong>Type</strong></td>
                    <td>{{ $product->type }}</td>
                </tr>
                <tr>
                    <td><strong>Price</strong></td>
                    <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Weight</strong></td>
                    <td>{{ number_format($product->weight, 0, ',', '.') }} grams</td>
                </tr>
                <tr>
                    <td><strong>Created At</strong></td>
                    <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Updated At</strong></td>
                    <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </tbody>
        </table>

        @if (auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
            <x-volt-link-button url="{{ route('product.edit', $product->id) }}" icon="edit" label="Edit" />
        @endif
        <x-volt-link-button url="{{ route('product.index') }}" icon="arrow left" label="Back" />
    </x-volt-panel>
</x-volt-app>
