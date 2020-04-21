<template>
  <div>
    <div>
      Prefix: <input
        type="text"
        v-model="prefix"
      >
    </div>

    <br>

    <div>
      <textarea
        v-model="html"
        cols="100"
        rows="20"
        placeholder="Paste or write your html with non-prefixed tailwind classes"
        @change="applyPrefix"
      ></textarea>
    </div>

    <br>

    <div>
      <textarea
        v-model="prefixed"
        cols="100"
        rows="20"
      ></textarea>
    </div>
  </div>
</template>

<script>
import tailwindClasses from '@/views/tailwind-classes';

export default {
  mixins: [
    tailwindClasses, //
  ],

  data() {
    return {
      prefix: 'tw-',
      html: '',
      prefixed: '',
    };
  },

  watch: {
    prefix() {
      this.applyPrefix();
    },

    html() {
      this.applyPrefix();
    },
  },

  methods: {
    applyPrefix() {
      const escapeRegExp = (s) => s.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');

      let code = this.html;

      this.classes.forEach((cls) => {
        code = code.replace(
          new RegExp(
            `(["':\\s])(?!${this.prefix})(-?${escapeRegExp(cls)})(?![-/])`,
            'g',
          ),
          `$1${this.prefix}$2`,
        );
      });

      this.prefixed = code;
    },
  },
};
</script>
