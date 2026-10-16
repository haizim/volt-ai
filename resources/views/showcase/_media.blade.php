<x-volt-panel title="Media Library">
    <p class="ui message info">
        <i class="info circle icon"></i>
        Komponen <code>x-volt-media-library</code> digunakan untuk mengelola file attachment pada model. Di bawah ini adalah contoh dengan collection kosong.
    </p>
    <x-volt-media-library
        name="attachments"
        :collection="collect()"
        multiple
        accept="image/*"
    />
</x-volt-panel>
