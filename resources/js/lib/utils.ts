import type { ClassValue } from "clsx"
import { clsx } from "clsx"
import { twMerge } from "tailwind-merge"
import { usePage } from '@inertiajs/vue3'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function toUrl(href: string): string {
    return href
}

export function urlIsActive(url: any): boolean {
    return usePage().url === url
}

export function formatPrice(
    price: number | null | undefined,
    currency: string = 'MDL',
): string {
    if (!price) return '—';

    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(price);
}
