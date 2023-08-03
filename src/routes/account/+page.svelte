<script>
	import { AppBar } from '@skeletonlabs/skeleton';
    export let data;
    // console.log(data);
</script>

<AppBar>
	<svelte:fragment slot="lead">(icon)</svelte:fragment>
	<svelte:fragment slot="trail"><a>Hello</a><a>Hello2</a><a>Hello3</a></svelte:fragment>
	<svelte:fragment slot="headline"><h1 class="text-4xl font-black"><a href="/">SUS App</a></h1></svelte:fragment>
</AppBar>

{#each data.data as d}
    <p>{JSON.stringify(d)}</p>
{/each}