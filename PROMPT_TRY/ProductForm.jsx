import { useState, useMemo, useCallback } from 'react';

/**
 * Form tambah produk — siap import di app React + Tailwind.
 *
 * Contoh:
 * import ProductForm from './PROMPT_TRY/ProductForm';
 * <ProductForm onSubmitSuccess={(data) => console.log(data)} />
 */

const CATEGORIES = [
  { value: 'Elektronik', label: 'Elektronik' },
  { value: 'Pakaian', label: 'Pakaian' },
  { value: 'Makanan', label: 'Makanan' },
];

const initialForm = {
  name: '',
  price: '',
  stock: '',
  category: '',
};

/** Mengembalikan objek error per field; kosong jika semua valid. */
function validateProductForm(values) {
  const errors = {};

  // Nama: minimal 5 karakter (setelah trim spasi depan/belakang)
  const name = values.name.trim();
  if (name.length < 5) {
    errors.name = 'Nama produk minimal 5 karakter.';
  }

  // Harga: harus angka valid, > 0 (tidak nol, tidak negatif)
  const priceNum = Number(values.price);
  if (values.price === '' || Number.isNaN(priceNum)) {
    errors.price = 'Harga wajib diisi dengan angka yang valid.';
  } else if (priceNum <= 0) {
    errors.price = 'Harga harus lebih besar dari 0.';
  }

  // Stok: sama seperti harga
  const stockNum = Number(values.stock);
  if (values.stock === '' || Number.isNaN(stockNum)) {
    errors.stock = 'Stok wajib diisi dengan angka yang valid.';
  } else if (stockNum <= 0) {
    errors.stock = 'Stok harus lebih besar dari 0.';
  }

  // Kategori: harus salah satu opsi dropdown
  const allowed = CATEGORIES.map((c) => c.value);
  if (!values.category || !allowed.includes(values.category)) {
    errors.category = 'Pilih salah satu kategori.';
  }

  return errors;
}

/** Kelas pesan error: di kanan input, warna merah dipaksa agar tidak “pucat”/tertimpa CSS global. */
const FIELD_ERROR_TEXT =
  'shrink-0 basis-[min(11rem,42%)] max-w-[min(14rem,48%)] pt-0.5 text-right text-sm font-medium leading-snug !text-red-600 [color:rgb(220,38,38)] dark:!text-red-300 dark:[color:rgb(252,165,165)]';

/** Versi lebih sempit untuk kolom Harga/Stok (grid 2 kolom). */
const FIELD_ERROR_TEXT_COMPACT =
  'shrink-0 basis-[min(8rem,36%)] max-w-[min(10rem,44%)] pt-0.5 text-right text-xs font-medium leading-snug sm:text-sm !text-red-600 [color:rgb(220,38,38)] dark:!text-red-300 dark:[color:rgb(252,165,165)]';

export default function ProductForm({ onSubmitSuccess, className = '' }) {
  const [form, setForm] = useState(initialForm);
  const [touched, setTouched] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Error dihitung dari nilai form saat ini (sumber kebenaran satu: validateProductForm)
  const errors = useMemo(() => validateProductForm(form), [form]);
  const isValid = useMemo(() => Object.keys(errors).length === 0, [errors]);

  const markTouched = useCallback((field) => {
    setTouched((prev) => ({ ...prev, [field]: true }));
  }, []);

  const handleChange = (field) => (e) => {
    const value = e.target.value;
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const handleBlur = (field) => () => {
    markTouched(field);
  };

  const showError = (field) => touched[field] && errors[field];

  const handleSubmit = async (e) => {
    e.preventDefault();
    // Setelah submit, tampilkan error semua field yang belum disentuh
    setTouched({
      name: true,
      price: true,
      stock: true,
      category: true,
    });

    const currentErrors = validateProductForm(form);
    if (Object.keys(currentErrors).length > 0 || isSubmitting) return;

    setIsSubmitting(true);
    try {
      const payload = {
        name: form.name.trim(),
        price: Number(form.price),
        stock: Number(form.stock),
        category: form.category,
      };
      // Ganti dengan API Anda; di sini simulasi async
      await new Promise((r) => setTimeout(r, 600));
      onSubmitSuccess?.(payload);
      setForm(initialForm);
      setTouched({});
    } finally {
      setIsSubmitting(false);
    }
  };

  const submitDisabled = !isValid || isSubmitting;

  return (
    <div
      className={`mx-auto w-full max-w-2xl rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 dark:border-slate-700 dark:bg-slate-900 ${className}`}
    >
      <h2 className="text-xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
        Tambah produk
      </h2>
      <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Lengkapi data di bawah untuk menambahkan produk ke inventaris.
      </p>

      <form className="mt-6 space-y-5" onSubmit={handleSubmit} noValidate>
        {/* Nama produk */}
        <div>
          <label
            htmlFor="product-name"
            className="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
          >
            Nama produk
          </label>
          <div className="flex min-w-0 items-start gap-3">
            <input
              id="product-name"
              name="name"
              type="text"
              autoComplete="off"
              value={form.name}
              onChange={handleChange('name')}
              onBlur={handleBlur('name')}
              className="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400"
              placeholder="Contoh: Kopi Arabika 250g"
              aria-invalid={showError('name') ? 'true' : 'false'}
              aria-describedby={showError('name') ? 'err-name' : undefined}
            />
            {showError('name') && (
              <p id="err-name" className={FIELD_ERROR_TEXT} role="alert">
                {errors.name}
              </p>
            )}
          </div>
        </div>

        <div className="grid gap-5 sm:grid-cols-2">
          {/* Harga */}
          <div className="min-w-0">
            <label
              htmlFor="product-price"
              className="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
            >
              Harga (Rp)
            </label>
            <div className="flex min-w-0 items-start gap-2">
              <input
                id="product-price"
                name="price"
                type="number"
                inputMode="decimal"
                min="0"
                step="0.01"
                value={form.price}
                onChange={handleChange('price')}
                onBlur={handleBlur('price')}
                className="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400"
                placeholder="0"
                aria-invalid={showError('price') ? 'true' : 'false'}
                aria-describedby={showError('price') ? 'err-price' : undefined}
              />
              {showError('price') && (
                <p id="err-price" className={FIELD_ERROR_TEXT_COMPACT} role="alert">
                  {errors.price}
                </p>
              )}
            </div>
          </div>

          {/* Stok */}
          <div className="min-w-0">
            <label
              htmlFor="product-stock"
              className="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
            >
              Stok
            </label>
            <div className="flex min-w-0 items-start gap-2">
              <input
                id="product-stock"
                name="stock"
                type="number"
                inputMode="numeric"
                min="0"
                step="1"
                value={form.stock}
                onChange={handleChange('stock')}
                onBlur={handleBlur('stock')}
                className="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400"
                placeholder="0"
                aria-invalid={showError('stock') ? 'true' : 'false'}
                aria-describedby={showError('stock') ? 'err-stock' : undefined}
              />
              {showError('stock') && (
                <p id="err-stock" className={FIELD_ERROR_TEXT_COMPACT} role="alert">
                  {errors.stock}
                </p>
              )}
            </div>
          </div>
        </div>

        {/* Kategori */}
        <div>
          <label
            htmlFor="product-category"
            className="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
          >
            Kategori
          </label>
          <div className="flex min-w-0 items-start gap-3">
            <select
              id="product-category"
              name="category"
              value={form.category}
              onChange={handleChange('category')}
              onBlur={handleBlur('category')}
              className="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400"
              aria-invalid={showError('category') ? 'true' : 'false'}
              aria-describedby={showError('category') ? 'err-category' : undefined}
            >
              <option value="" disabled>
                Pilih kategori
              </option>
              {CATEGORIES.map((c) => (
                <option key={c.value} value={c.value}>
                  {c.label}
                </option>
              ))}
            </select>
            {showError('category') && (
              <p id="err-category" className={FIELD_ERROR_TEXT} role="alert">
                {errors.category}
              </p>
            )}
          </div>
        </div>

        <div className="pt-2">
          <button
            type="submit"
            disabled={submitDisabled}
            className="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-slate-900"
          >
            {isSubmitting ? 'Menyimpan…' : 'Simpan produk'}
          </button>
        </div>
      </form>
    </div>
  );
}
