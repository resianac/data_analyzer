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
