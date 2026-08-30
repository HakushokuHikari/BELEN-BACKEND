<x-layout>
  <x-slot name="title">Contact</x-slot>
  <x-nav :href="route('home')">Homepage</x-nav>
  <x-nav :href="route('contact')">Contact</x-nav>
  <x-nav :href="route('services')">Services</x-nav>
  <x-nav :href="route('about')">About</x-nav>
  Hello from Contact!
</x-layout>