<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">

        <div class="container">
            <h1>Create New Product</h1>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Product Name -->
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name"
                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price"
                        class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required
                        min="0" step="0.01">
                    @error('price')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Season -->
                <div class="form-group">
                    <label for="season">Season</label>
                    <select id="season" name="season" class="form-control @error('season') is-invalid @enderror"
                        required>
                        <option value="">Select Season</option>
                        <option value="summer" {{ old('season') == 'summer' ? 'selected' : '' }}>Summer</option>
                        <option value="winter" {{ old('season') == 'winter' ? 'selected' : '' }}>Winter</option>
                        <option value="all" {{ old('season') == 'all' ? 'selected' : '' }}>All Seasons</option>
                    </select>
                    @error('season')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <label for="images">Product Images:</label>
                <input type="file" name="images[]" id="images" multiple> <!-- `multiple` allows multiple uploads -->


                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </div>
</body>

</html>
