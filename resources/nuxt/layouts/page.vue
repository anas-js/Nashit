<template>
  <div ref="el" class="page-layout">
    <div>
      <div class="rigth">
        <NuxtLink :to="localePath('/')">
          <ImageNashit
            ref="logo"
            class="logo"
            :class="{ ai: $route.path === '/' }"
            anime-name="rigth-in"
            next-anime
            anime-before="500"></ImageNashit>
        </NuxtLink>
        <div class="group-text">
          <h1 v-if="$route.params.id">{{ layoutDataStore.title }}</h1>
          <div class="elements">
            <div class="list">
              <ul v-if="$route.params.profile">
                <li>
                  <NuxtLink :to="localePath(`/${$route.params.profile}`)" exact>
                    <i class="ri-user-line"></i>
                    {{ profile.name }}
                  </NuxtLink>
                </li>
              </ul>
              <template v-if="path">
                <ul>
                  <template v-if="path === 'course'">
                    <li v-if="layoutDataStore.data.value.finish">
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'CourseFinishPage',
                            params: $route.params,
                          })
                        ">
                        <i class="ri-flag-2-line"></i>
                        {{ $t(`${lang}.course.link.finish`) }}
                      </NuxtLink>
                    </li>
                    <template v-else>
                      <li>
                        <NuxtLink
                          :to="
                            localePath({
                              name: 'CourseStatsPage',
                              params: $route.params,
                            })
                          ">
                          <i class="ri-pie-chart-line"></i>
                          {{ $t(`${lang}.course.link.stats`) }}
                        </NuxtLink>
                      </li>
                      <li>
                        <NuxtLink
                          :to="
                            localePath({
                              name: 'CourseTodayPage',
                              params: $route.params,
                            })
                          ">
                          <i class="ri-apps-2-line"></i>
                          {{ $t(`${lang}.course.link.today`) }}
                        </NuxtLink>
                      </li>
                    </template>

                    <li>
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'CourseAllPage',
                            params: $route.params,
                          })
                        ">
                        <i class="ri-archive-line"></i>
                        {{ $t(`${lang}.course.link.all`) }}
                      </NuxtLink>
                    </li>

                    <li v-if="!$route.params.profile">
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'CourseSettingsPage',
                            params: { id: $route.params.id },
                          })
                        ">
                        <i class="ri-settings-3-line"></i>
                        {{ $t(`${lang}.course.link.settings`) }}
                      </NuxtLink>
                    </li>

                    <li v-if="$auth.loggedIn">
                      <button
                        class="copy"
                        @click="course().copy().showCopyPOP()">
                        <i class="ri-file-copy-line"></i>
                        {{ $t(`${lang}.course.buttons.copy`) }}
                      </button>
                      <PopUp
                        ref="copyCourse"
                        class="copy-Course-popup"
                        :title="$t(`${lang}.course.copy.title`)"
                        :btn="$t(`${lang}.board.pops.copy.button`)"
                        btn-icon="ri-file-copy-line"
                        @on-click-btn="course().copy().done()">
                        <div>
                          <div class="done">
                            <h2>{{ $t(`${lang}.course.copy.done.title`) }}</h2>
                            <div class="data">
                              <div class="done-day">
                                <label>
                                  {{
                                    $t(`${lang}.course.copy.done.days.label`)
                                  }}
                                </label>
                                <input
                                  v-model.number="courseLayout.copyCourse.day"
                                  type="number"
                                  placeholder="15"
                                  @input="course().copy().clacDays()" />
                                <!-- <p
                                  :class="{
                                    visible:
                                      courseLayout.copyCourse.errMsgs.day,
                                  }"
                                  class="errMsg v-hiddin">
                                  خطأ
                                </p> -->
                              </div>
                              <div class="type-doing">
                                <label>
                                  {{
                                    $t(`${lang}.course.copy.done.weekend.added`)
                                  }}
                                </label>
                                <ElementsToogleButton
                                  :set="
                                    courseLayout.copyCourse.isExtraDaysWeekend
                                  "
                                  @toggle="
                                    course()
                                      .copy()
                                      .extraDaysWeekendToggle($event)
                                  " />
                              </div>
                              <div class="weekend">
                                <label>
                                  {{
                                    $t(`${lang}.course.copy.done.weekend.label`)
                                  }}
                                </label>
                                <div class="days">
                                  <div v-for="(d, index) in days" :key="index">
                                    <span
                                      :class="{
                                        active:
                                          courseLayout.copyCourse.weekend.find(
                                            (e) => e == index
                                          ) !== undefined,
                                      }"
                                      @click="
                                        course().copy().addWeekend(index)
                                      ">
                                      {{ d }}
                                    </span>
                                  </div>
                                </div>
                              </div>

                              <div class="result">
                                <div class="lessons">
                                  {{
                                    $t(`${lang}.course.copy.done.result.text.0`)
                                  }}
                                  <span>
                                    {{
                                      courseLayout.copyCourse.result.lessons
                                        .value === 0
                                        ? "X"
                                        : courseLayout.copyCourse.result.lessons
                                            .value
                                    }}
                                  </span>
                                  {{
                                    courseLayout.copyCourse.result.lessons.word
                                  }}
                                </div>
                                <div class="days">
                                  {{
                                    $t(`${lang}.course.copy.done.result.text.1`)
                                  }}
                                  <span>
                                    {{
                                      courseLayout.copyCourse.result.days
                                        .value === 0
                                        ? "X"
                                        : courseLayout.copyCourse.result.days
                                            .value
                                    }}
                                  </span>
                                  {{ courseLayout.copyCourse.result.days.word }}
                                  {{
                                    $t(`${lang}.course.copy.done.result.text.2`)
                                  }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <FullLoading v-if="courseLayout.copyCourse.loading" />
                      </PopUp>
                    </li>
                  </template>
                  <template v-if="path === 'board'">
                    <li>
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'BaordStatsPage',
                            params: $route.params,
                          })
                        ">
                        <i class="ri-pie-chart-line"></i>
                        {{ $t(`${lang}.board.link.stats`) }}
                      </NuxtLink>
                    </li>
                    <li>
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'BaordIndexPage',
                            params: $route.params,
                          })
                        "
                        exact>
                        <i class="ri-apps-2-line"></i>
                        {{ $t(`${lang}.board.link.board`) }}
                      </NuxtLink>
                    </li>
                    <li v-if="!$route.params.profile">
                      <NuxtLink
                        :to="
                          localePath({
                            name: 'BaordSettingsPage',
                            params: { id: $route.params.id },
                          })
                        ">
                        <i class="ri-settings-3-line"></i>
                        {{ $t(`${lang}.board.link.settings`) }}
                      </NuxtLink>
                    </li>

                    <li v-if="$auth.loggedIn">
                      <button class="copy" @click="board().showCopyPOP()">
                        <i class="ri-file-copy-line"></i>
                        {{ $t(`${lang}.board.buttons.copy`) }}
                      </button>
                      <PopUp
                        ref="copyBoard"
                        class="copy-Board-popup"
                        :title="$t(`${lang}.board.pops.copy.title`)"
                        :btn="$t(`${lang}.board.pops.copy.button`)"
                        btn-icon="ri-file-copy-line"
                        @on-click-btn="board().copy()">
                        <div>
                          <ElementsSelect
                            :select="boardLayout.copyBoard.type"
                            :elements="[
                              {
                                title: $t(`${lang}.board.pops.copy.withTasks`),
                                value: 'with-tasks',
                              },
                              {
                                title: $t(
                                  `${lang}.board.pops.copy.withoutTasks`
                                ),
                                value: 'without-tasks',
                              },
                            ]"
                            :linear="true"
                            @on-select="
                              boardLayout.copyBoard.type = $event.value
                            " />
                        </div>

                        <FullLoading v-if="boardLayout.copyBoard.loading" />
                      </PopUp>
                    </li>
                    <li v-if="!$route.params.profile">
                      <button
                        class="create-todo"
                        @click="board().showCreateTodo()">
                        <i class="ri-draft-line"></i>
                        {{ $t(`${lang}.board.buttons.create`) }}
                      </button>
                    </li>
                  </template>

                  <li
                    v-if="
                      !layoutDataStore.data.value.private &&
                      (path === 'board' || path === 'course')
                    ">
                    <button @click="sharing()">
                      <i class="ri-share-line"></i>
                      {{ $t(`${lang}.global.buttons.share`) }}
                    </button>
                  </li>
                  <li>
                    <NuxtLink
                      :to="
                        localePath({
                          name: getRouterName('ReadPage'),
                          params: $route.params,
                        })
                      ">
                      <i class="ri-book-read-line"></i>
                      {{ $t(`${lang}.global.link.read`) }}
                    </NuxtLink>
                  </li>
                </ul>
              </template>
            </div>

            <div class="global-list">
              <h2>{{ $t(`${lang}.global.title`) }}</h2>
              <ul>
                <li>
                  <NuxtLink :to="localePath(`/`)" exact>
                    <i class="ri-home-2-line"></i>
                    {{ $t(`${lang}.global.link.main`) }}
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink :to="localePath(`/dashboard/${getDashBoardPath}`)">
                    <i class="ri-corner-up-right-line"></i>
                    {{ $t(`${lang}.global.link.dashboard`) }}
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink
                    :to="
                      localePath({
                        name: getRouterName('PrefsPage'),
                        params: $route.params,
                      })
                    ">
                    <i class="ri-settings-6-line"></i>
                    {{ $t(`${lang}.global.link.prefs`) }}
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink
                    v-if="!pwa()"
                    :to="
                      localePath({
                        name: getRouterName('InstallPage'),
                        params: $route.params,
                      })
                    ">
                    <i class="ri-box-1-line"></i>
                    {{ $t(`${lang}.global.link.install`) }}
                  </NuxtLink>
                  <button v-else @click="!pwa().prompt()">
                    <i class="ri-box-1-line"></i>
                    {{ $t(`${lang}.global.link.install`) }}
                  </button>
                </li>
                <li>
                  <!-- <NuxtLink
                      :to="localePath(`/${path}/${$route.params.id}/report`)"
                      ><i class="ri-alert-line"></i
                      >{{ $t(`${lang}.global.link.report`) }}</NuxtLink
                    > -->
                  <a target="blank" href="https://www.instagram.com/juzr.official/">
                    <i class="ri-alert-line"></i>
                    {{ $t(`${lang}.global.link.report`) }}
                  </a>
                </li>
                <li>
                  <NuxtLink
                    :to="
                      localePath({
                        name: getRouterName('AboutPage'),
                        params: $route.params,
                      })
                    ">
                    <i class="ri-information-line"></i>
                    {{ $t(`${lang}.global.link.about`) }}
                  </NuxtLink>
                </li>
                <!-- <template v-if="!$route.params.profile">



                </template> -->
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="left">
        <div class="user">
          <PrefsBtns></PrefsBtns>
          <nav-user v-if="$auth.loggedIn"></nav-user>
          <div class="buttons" v-else>
          <NuxtLink
            :to="localePath(`${$app().FR_URL.juzr}/login`)"
            :external="true"
            class="login">
            {{ $t(`${lang}.buttons.login`) }}
          </NuxtLink>

          <NuxtLink
            class="login register"
            :to="localePath(`${$app().FR_URL.juzr}/register`)"
            :external="true">
            {{ $t(`${lang}.buttons.register`) }}
          </NuxtLink>
        </div>
        </div>

        <div>
          <!-- :class="{smallContent : $route.path.split('/')[2] !== 'stats'}" -->
          <div class="container">
            <p class="offline-alert alert warning" v-if="systemStore.offline">
              <i class="ri-folder-warning-line"></i>
              {{ $t(`${lang}.noInternet`) }}
            </p>
          </div>
          <div class="content slots-content">
            <slot></slot>
          </div>
        </div>
      </div>
      <div
        ref="bottom_list"
        class="bottom"
        :class="{ active: actions.animeRun || actions.listBottomShow }">
        <h3 ref="bottom_list_title">{{ layoutDataStore.title }}</h3>
        <div class="list" @click="closeBottomList">
          <ul>
            <li v-if="$route.params.profile">
              <NuxtLink :to="localePath(`/${$route.params.profile}`)" exact>
                <i class="ri-user-line"></i>
                {{ profile.name }}
              </NuxtLink>
            </li>
            <template v-if="path">
              <!-- <ul> -->
              <template v-if="path === 'course'">
                <li v-if="layoutDataStore.data.value.finish">
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'CourseFinishPage',
                        params: $route.params,
                      })
                    ">
                    <i class="ri-flag-2-line"></i>
                    {{ $t(`${lang}.course.link.finish`) }}
                  </NuxtLink>
                </li>

                <template v-else>
                  <li>
                    <NuxtLink
                      :to="
                        localePath({
                          name: 'CourseStatsPage',
                          params: $route.params,
                        })
                      ">
                      <i class="ri-pie-chart-line"></i>
                      {{ $t(`${lang}.course.link.stats`) }}
                    </NuxtLink>
                  </li>
                  <li>
                    <NuxtLink
                      :to="
                        localePath({
                          name: 'CourseTodayPage',
                          params: $route.params,
                        })
                      ">
                      <i class="ri-apps-2-line"></i>
                      {{ $t(`${lang}.course.link.today`) }}
                    </NuxtLink>
                  </li>
                </template>

                <li>
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'CourseAllPage',
                        params: $route.params,
                      })
                    ">
                    <i class="ri-archive-line"></i>
                    {{ $t(`${lang}.course.link.all`) }}
                  </NuxtLink>
                </li>

                <li v-if="!$route.params.profile">
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'CourseSettingsPage',
                        params: { id: $route.params.id },
                      })
                    ">
                    <i class="ri-settings-3-line"></i>
                    {{ $t(`${lang}.course.link.settings`) }}
                  </NuxtLink>
                </li>

                <li v-if="$auth.loggedIn">
                  <button class="copy" @click="course().copy().showCopyPOP()">
                    <i class="ri-file-copy-line"></i>
                    {{ $t(`${lang}.course.buttons.copy`) }}
                  </button>
                </li>
              </template>
              <template v-if="path === 'board'">
                <li>
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'BaordStatsPage',
                        params: $route.params,
                      })
                    ">
                    <i class="ri-pie-chart-line"></i>
                    {{ $t(`${lang}.board.link.stats`) }}
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'BaordIndexPage',
                        params: $route.params,
                      })
                    "
                    exact>
                    <i class="ri-apps-2-line"></i>
                    {{ $t(`${lang}.board.link.board`) }}
                  </NuxtLink>
                </li>
                <li v-if="!$route.params.profile">
                  <NuxtLink
                    :to="
                      localePath({
                        name: 'BaordSettingsPage',
                        params: { id: $route.params.id },
                      })
                    ">
                    <i class="ri-settings-3-line"></i>
                    {{ $t(`${lang}.board.link.settings`) }}
                  </NuxtLink>
                </li>

                <li v-if="$auth.loggedIn">
                  <button
                    class="copy"
                    @click="($refs['copyBoard'] as any).show()">
                    <i class="ri-file-copy-line"></i>
                    {{ $t(`${lang}.board.buttons.copy`) }}
                  </button>
                  <!-- <PopUp
                      ref="copyBoard"
                      class="copy-Board-popup"
                      :title="$t(`${lang}.board.pops.copy.title`)"
                      :btn="$t(`${lang}.board.pops.copy.button`)"
                      btn-icon="ri-file-copy-line"
                       @on-click-btn="board().copy()"
                    >
                      <div>
                        <ElementsSelect
                          :select="boardLayout.copyBoard.type"
                          :elements="[
                            {
                              title: $t(`${lang}.board.pops.copy.withTasks`),
                              value: 'with-tasks',
                            },
                            {
                              title: $t(`${lang}.board.pops.copy.withoutTasks`),
                              value: 'without-tasks',
                            },
                          ]"
                          :linear="true"
                          @on-select="boardLayout.copyBoard.type = $event.value"
                        />
                      </div>

                      <FullLoading v-if="boardLayout.copyBoard.loading" />
                    </PopUp> -->
                </li>
                <li v-if="!$route.params.profile">
                  <button class="create-todo" @click="board().showCreateTodo()">
                    <i class="ri-draft-line"></i>
                    {{ $t(`${lang}.board.buttons.create`) }}
                  </button>
                </li>
              </template>

              <li
                v-if="
                  !layoutDataStore.data.value.private &&
                  (path === 'board' || path === 'course')
                ">
                <button @click="sharing()">
                  <i class="ri-share-line"></i>
                  {{ $t(`${lang}.global.buttons.share`) }}
                </button>
              </li>
              <li>
                <NuxtLink
                  :to="
                    localePath({
                      name: getRouterName('ReadPage'),
                      params: $route.params,
                    })
                  ">
                  <i class="ri-book-read-line"></i>
                  {{ $t(`${lang}.global.link.read`) }}
                </NuxtLink>
              </li>
              <!-- </ul> -->
              <li class="btn-more" :class="{ close: actions.listBottomShow }">
                <button @click="bottomListShow()">
                  <i
                    :class="{
                      'ri-arrow-up-line': !actions.listBottomShow,
                      'ri-close-line': actions.listBottomShow,
                    }"></i>
                  <span v-if="!actions.listBottomShow">
                    {{ $t(`${lang}.global.more`) }}
                  </span>
                  <span v-else>{{ $t(`${lang}.global.close`) }}</span>
                </button>
              </li>
            </template>
            <!-- <template v-if="path">
              <template v-if="path === 'course'">
                <li v-if="layoutDataStore.data.value.courseFinish">
                  <NuxtLink
                    :to="
                      localePath(
                        `${
                          $route.params.profile
                            ? '/' + $route.params.profile
                            : ''
                        }/course/${$route.params.id}/finish`
                      )
                    "
                    ><i class="ri-flag-2-line"></i>
                    {{ $t(`${lang}.course.link.finish`) }}</NuxtLink
                  >
                </li>
                <template v-else>
                  <li>
                    <NuxtLink
                      :to="
                        localePath(
                          `${
                            $route.params.profile
                              ? '/' + $route.params.profile
                              : ''
                          }/course/${$route.params.id}/stats`
                        )
                      "
                      ><i class="ri-pie-chart-line"></i
                      >{{ $t(`${lang}.course.link.stats`) }}</NuxtLink
                    >
                  </li>
                  <li>
                    <NuxtLink
                      :to="
                        localePath(
                          `${
                            $route.params.profile
                              ? '/' + $route.params.profile
                              : ''
                          }/course/${$route.params.id}/today`
                        )
                      "
                      ><i class="ri-apps-2-line"></i
                      >{{ $t(`${lang}.course.link.today`) }}</NuxtLink
                    >
                  </li>
                </template>
                
                <li>
                  <NuxtLink
                    :to="
                      localePath(
                        `${
                          $route.params.profile
                            ? '/' + $route.params.profile
                            : ''
                        }/course/${$route.params.id}/all`
                      )
                    "
                    ><i class="ri-archive-line"></i
                    >{{ $t(`${lang}.course.link.all`) }}</NuxtLink
                  >
                </li>

                <li v-if="!$route.params.profile">
                  <NuxtLink
                    :to="localePath(`/course/${$route.params.id}/settings`)"
                    ><i class="ri-settings-3-line"></i
                    >{{ $t(`${lang}.course.link.settings`) }}</NuxtLink
                  >
                </li>
                <li v-if="$auth.loggedIn">
                  <button class="copy" @click="course().copy()">
                    <i class="ri-file-copy-line"></i
                    >{{ $t(`${lang}.course.buttons.copy`) }}
                  </button>
                </li>

              </template>
              <template v-if="path === 'board'">
                <li>
                  <NuxtLink
                    :to="
                      localePath(
                        `${
                          $route.params.profile
                            ? '/' + $route.params.profile
                            : ''
                        }/board/${$route.params.id}/stats`
                      )
                    "
                    ><i class="ri-pie-chart-line"></i
                    >{{ $t(`${lang}.board.link.stats`) }}</NuxtLink
                  >
                </li>
                <li>
                  <NuxtLink
                    :to="
                      localePath(
                        `${
                          $route.params.profile
                            ? '/' + $route.params.profile
                            : ''
                        }/board/${$route.params.id}/`
                      )
                    "
                    exact
                    ><i class="ri-apps-2-line"></i
                    >{{ $t(`${lang}.board.link.board`) }}</NuxtLink
                  >
                </li>
                <li v-if="!$route.params.profile">
                  <NuxtLink
                    :to="localePath(`/board/${$route.params.id}/settings`)"
                    ><i class="ri-settings-3-line"></i
                    >{{ $t(`${lang}.board.link.settings`) }}</NuxtLink
                  >
                </li>

                <li v-if="$auth.loggedIn">
                  <button
                    class="copy"
                    @click="($refs['copyBoard'] as any).show()"
                  >
                    <i class="ri-file-copy-line"></i
                    >{{ $t(`${lang}.board.buttons.copy`) }}
                  </button>
                </li>
                <li v-if="!$route.params.profile">
                  <button class="create-todo" @click="board().showCreateTodo()">
                    <i class="ri-draft-line"></i
                    >{{ $t(`${lang}.board.buttons.create`) }}
                  </button>
                </li>
              </template>

              <li
                v-if="
                  !layoutDataStore.data.value.private &&
                  (path === 'board' || path === 'course')
                "
              >
                <button @click="sharing()">
                  <i class="ri-share-line"></i>
                  {{ $t(`${lang}.global.buttons.share`) }}
                </button>
              </li>
              <li>
                <NuxtLink :to="localePath(`/${path}/${$route.params.id}/read`)"
                  ><i class="ri-book-read-line"></i
                  >{{ $t(`${lang}.global.link.read`) }}</NuxtLink
                >
              </li>

              <li class="btn-more" :class="{ close: actions.listBottomShow }">
                <button @click="bottomListShow()">
                  <i
                    :class="{
                      'ri-arrow-up-line': !actions.listBottomShow,
                      'ri-close-line': actions.listBottomShow,
                    }"
                  ></i>
                  <span v-if="!actions.listBottomShow">{{
                    $t(`${lang}.global.more`)
                  }}</span>
                  <span v-else>{{ $t(`${lang}.global.close`) }}</span>
                </button>
              </li>
            </template> -->
          </ul>

          <div class="global-list">
            <h4 v-if="path">
              {{ $t(`${lang}.global.title`) }}
            </h4>
            <ul :class="{ noPath: !path }">
              <li>
                <NuxtLink :to="localePath(`/`)" exact>
                  <i class="ri-home-2-line"></i>
                  {{ $t(`${lang}.global.link.main`) }}
                </NuxtLink>
              </li>
              <li>
                <NuxtLink :to="localePath(`/dashboard/${getDashBoardPath}`)">
                  <i class="ri-corner-up-right-line"></i>
                  {{ $t(`${lang}.global.link.dashboard`) }}
                </NuxtLink>
              </li>

              <li>
                <NuxtLink
                  :to="
                    localePath({
                      name: getRouterName('PrefsPage'),
                      params: $route.params,
                    })
                  ">
                  <i class="ri-settings-6-line"></i>
                  {{ $t(`${lang}.global.link.prefs`) }}
                </NuxtLink>
              </li>
              <li>
                <NuxtLink
                  v-if="!pwa()"
                  :to="
                    localePath({
                      name: getRouterName('InstallPage'),
                      params: $route.params,
                    })
                  ">
                  <i class="ri-box-1-line"></i>
                  {{ $t(`${lang}.global.link.install`) }}
                </NuxtLink>
                <button v-else @click="!pwa().prompt()">
                  <i class="ri-box-1-line"></i>
                  {{ $t(`${lang}.global.link.install`) }}
                </button>
              </li>
              <li>
                <!-- <NuxtLink
                      :to="localePath(`/${path}/${$route.params.id}/report`)"
                      ><i class="ri-alert-line"></i
                      >{{ $t(`${lang}.global.link.report`) }}</NuxtLink
                    > -->
                <a target="blank" href="https://www.instagram.com/juzr.official/">
                  <i class="ri-alert-line"></i>
                  {{ $t(`${lang}.global.link.report`) }}
                </a>
              </li>
              <li>
                <NuxtLink
                  :to="
                    localePath({
                      name: getRouterName('AboutPage'),
                      params: $route.params,
                    })
                  ">
                  <i class="ri-information-line"></i>
                  {{ $t(`${lang}.global.link.about`) }}
                </NuxtLink>
              </li>

              <!-- <template v-if="path">
                  <li>
                    <NuxtLink
                      :to="localePath(`/${path}/${$route.params.id}/prefs`)"
                      ><i class="ri-settings-6-line"></i
                      >{{ $t(`${lang}.global.link.prefs`) }}</NuxtLink
                    >
                  </li>
                  <li>
                    <NuxtLink
                      v-if="!pwa()"
                      :to="localePath(`/${path}/${$route.params.id}/install`)"
                      ><i class="ri-box-1-line"></i
                      >{{ $t(`${lang}.global.link.install`) }}</NuxtLink
                    >
                    <button v-else @click="!pwa().prompt()">
                      <i class="ri-box-1-line"></i
                      >{{ $t(`${lang}.global.link.install`) }}
                    </button>
                  </li>
                  <li>

                    <a target="blank" href="https://www.instagram.com/juzr.official/"
                      ><i class="ri-alert-line"></i
                      >{{ $t(`${lang}.global.link.report`) }}</a
                    >
                  </li>
                  <li>
                    <NuxtLink
                      :to="localePath(`/${path}/${$route.params.id}/about`)"
                      ><i class="ri-information-line"></i
                      >{{ $t(`${lang}.global.link.about`) }}</NuxtLink
                    >
                  </li>
                </template>
                <template v-else>
                  <li>
                    <NuxtLink :to="localePath(`/prefs`)"
                      ><i class="ri-settings-6-line"></i
                      >{{ $t(`${lang}.global.link.prefs`) }}</NuxtLink
                    >
                  </li>

                  <li>
                    <NuxtLink
                      v-if="!pwa()"
                      :to="localePath(`/install`)"
                      ><i class="ri-box-1-line"></i
                      >{{ $t(`${lang}.global.link.install`) }}</NuxtLink
                    >
                    <button v-else @click="!pwa().prompt()">
                      <i class="ri-box-1-line"></i
                      >{{ $t(`${lang}.global.link.install`) }}
                    </button>
                  </li>

                  <li>

                    <a target="blank" href="https://www.instagram.com/juzr.official/"
                      ><i class="ri-alert-line"></i
                      >{{ $t(`${lang}.global.link.report`) }}</a
                    >
                  </li>

                  <li>
                    <NuxtLink :to="localePath(`/about`)"
                      ><i class="ri-information-line"></i
                      >{{ $t(`${lang}.global.link.about`) }}</NuxtLink
                    >
                  </li>
                </template> -->
              <template v-if="!$route.params.profile"></template>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- <msgBox></msgBox> -->
    <!-- <FullLoading v-if="courseLayout.copyCourse.loading" /> -->
  </div>
</template>

<script setup lang="ts">
// Strores
const route = useRoute();
// console.log(route.path)
const router = useRouter();
const layoutDataStore = useLayoutDataStore();
const systemStore = useSystemStore();
const localePath = useLocalePath();
const $auth = useAuth();
const el = ref();
// const {$pwa} = useNuxtApp();

function pwa() {
  return window?.pwa;
}

const { $anime } = useNuxtApp();

const getPathName = (path: string[]) => {
  if (route.params.profile) {
    return path[1];
  } else if (route.params.id) {
    return path[0];
  } else {
    return false;
  }
};

const profile = ref({}) as Ref<{
  username: string;
  name: string;
  description: string;
}>;

if (route.params.profile && !profile.value.username) {
  profile.value = await $api.get<{
    username: string;
    name: string;
    description: string;
  }>(`${$app().BK_URL.juzr}/profile/${route.params.profile}`, {
    params: {
      platform: "nashit",
    },
  });
}

const path = ref(getPathName(systemStore.path.split));

const getDashBoardPath = computed(
  () => (path.value ? path.value + "s" : null) || "active"
);
// console.log(systemStore.path.split);

// console.log(path.value,systemStore.path.split);
// console.log(route.meta)
//  window.router = router;
// console.log(route);

watch(
  () => route.path,
  () => {
    const routePathSplit = $help().getPathURL(route.path).array;
    path.value = getPathName(routePathSplit);
    // console.log(layoutDataStore.data.value.finish)
  }
);

onBeforeUnmount(() => {
  layoutDataStore.setData = false;
});

const logo = ref();

onMounted(() => {
  onScrollActive({
    from: 0,
    to: 50,
    callback: (value) => {
      logo.value.image.style.filter = `blur(${value}px)`;
    },
    target: 16,
    fromWidth: 700,
  });
});
// varibles
const lang = "layouts.page";

const screen = reactive({
  titleListBottomHeight: 0,
});

const actions = reactive({
  listBottomShow: false,
  animeRun: false,
  closeRun: false,
});

// const actions = reactive({
//   listBottomShow: false,
//   animeRun: false,
//   closeRun: false,
// });

const boardLayout = reactive({
  copyBoard: {
    type: "with-tasks",
    loading: false,
  },
});

// console.log(layoutDataStore.data.value);

const courseLayout = reactive({
  copyCourse: {
    day: layoutDataStore.data.value?.days || 1,
    errMsgs: {
      name: false,
      lessons: false,
      day: false,
    },
    result: {
      lessons: {
        value: 0,
        word: $t(`gl.lessons.0`),
      },
      days: {
        value: 0,
        word: $t(`gl.days.0`),
      },
    },
    weekend: [...(layoutDataStore.data.value?.weekend || [])],
    loading: false,
    isExtraDaysWeekend: layoutDataStore.data.value?.inSpace || false,
  },
});

// refs
const bottom_list_title = ref() as Ref<HTMLHeadingElement>;
const bottom_list = ref() as Ref<HTMLUListElement>;
const copyBoard = ref();
const copyCourse = ref();
const days = ref($t(`gl.week`));
const trans = useNuxtApp().$i18n.t;
// const createURL = (to : string) => {
//   const url = [];
//   route.params.profile && url.push(route.params.profile);
//   path.value && url.push(path.value);
//   route.params.id && url.push(route.params.id);
//   url.push(to);

//   return url.join("/");
// };

// console.log(createURL("about"));
// funcion

const getRouterName = (name: string) => {
  if (path.value) {
    return path.value + name;
  }
  return name;
};

function bottomListShow() {
  if (!actions.animeRun && !actions.closeRun) {
    actions.animeRun = true;
    setTimeout(() => {
      const title = bottom_list_title.value;
      let heightBottomList = 0;
      let isClassSet = false;
      if (
        window.innerHeight / 1.7 <
        (bottom_list.value.querySelector(".list")?.clientHeight ?? 0)
      ) {
        heightBottomList = window.innerHeight / 1.7;
        isClassSet = true;
      } else {
        heightBottomList =
          (bottom_list.value.querySelector(".list")?.clientHeight ?? 0) + 100;
      }

      if (actions.listBottomShow) {
        bottom_list.value.querySelector(".list")?.classList.remove("small");

        $anime({
          targets: bottom_list_title.value,
          height: [screen.titleListBottomHeight, 0],
          marginBottom: [10, 0],
          marginTop: [10, 0],
          opacity: [1, 0],
          easing: "easeInOutCirc",
          complete: () => {
            title.style.display = "none";
            // console.log("run");
            actions.animeRun = false;
          },
        });

        $anime({
          targets: bottom_list.value,
          height: 76,
          easing: "easeInOutCirc",
        });
      } else {
        title.style.display = "block";

        if (!screen.titleListBottomHeight) {
          screen.titleListBottomHeight = title.clientHeight!;
        }

        // title.style.height = "auto";

        $anime({
          targets: bottom_list_title.value,
          height: [0, title.clientHeight || screen.titleListBottomHeight],
          opacity: [0, 1],
          easing: "easeInOutCirc",
          marginBottom: [0, 10],
          marginTop: [0, 10],
        });

        $anime({
          targets: bottom_list.value,
          height: heightBottomList,
          complete: () => {
            if (isClassSet) {
              bottom_list.value.querySelector(".list")?.classList.add("small");
            }

            actions.animeRun = false;
          },
          easing: "easeInOutCirc",
        });
      }

      actions.listBottomShow = !actions.listBottomShow;
    }, 100);
  }
}

function closeBottomList() {
  // const event = event;

  if (
    (event as unknown as EventTarget & { target: HTMLElement }).target
      .tagName !== "A" ||
    actions.closeRun
  ) {
    return;
  }

  actions.closeRun = true;
  actions.listBottomShow = false;

  const title = bottom_list_title.value;

  bottom_list.value.querySelector(".list")?.classList.remove("small");

  $anime({
    targets: bottom_list_title.value,
    height: [screen.titleListBottomHeight, 0],
    marginBottom: [10, 0],
    marginTop: [10, 0],
    opacity: [1, 0],
    easing: "easeInOutCirc",
    complete: () => {
      title.style.display = "none";

      actions.closeRun = false;
      // this.actions.animeRun = false;
    },
  });

  $anime({
    targets: bottom_list.value,
    height: 76,
    easing: "easeInOutCirc",
  });
}



function board() {
  return {
    copy() {
      boardLayout.copyBoard.loading = true;

      $msg({
        text: `${$t(`${lang}.board.msg.copy.sure.0`)} "${
          layoutDataStore.title
        }"${$t(`${lang}.board.msg.copy.sure.1`)}`,
        type: "sure",
        btns: {
          t() {
            $api
              .post(`${$app().BK_URL.nashit}/board/${route.params.id}/copy`, {
                body: {
                  type: boardLayout.copyBoard.type,
                },
              })
              .then((res) => {
                navigateTo(`/board/${res.id}`, {
                  external: true,
                });
                boardLayout.copyBoard.loading = false;

                copyBoard.value.close();
              })
              .catch((_) => {
                boardLayout.copyBoard.loading = false;

                $msg({
                  text: $t(`gl.msg.error.sendData`),
                  type: "error",
                });
              });
          },
          f() {
            boardLayout.copyBoard.loading = false;
          },
        },
      });
    },
    showCopyPOP() {
      copyBoard.value.show();
    },
    showCreateTodo() {

      if (el.value.querySelector(".showCreateTodo-btn")) {
        el.value
          .querySelector(".showCreateTodo-btn").click();
      } else {
        router.push(`/board/${route.params.id}`);
        $msg({
          text: $t(`${lang}.board.msg.showCreateTodo`),
          type: "error",
        });
      }
    },
  };
}

function course() {
  return {
    copy() {
      return {
        showCopyPOP() {
          this.clacDays();
          // courseLayout.copyCourse.loading = true;
          copyCourse.value.show();
        },
        done() {
          courseLayout.copyCourse.loading = true;
          $api
            .post<any>(
              `${$app().BK_URL.nashit}/course/${route.params.id}/copy`,
              {
                body: {
                  inSpace: !courseLayout.copyCourse.isExtraDaysWeekend,
                  done_days: courseLayout.copyCourse.day,
                  weekend: courseLayout.copyCourse.weekend,
                },
              }
            )
            .then((res) => {
              copyCourse.value.close();
              navigateTo(`/course/${res.id}/stats`, {
                external: true,
              });
              courseLayout.copyCourse.loading = false;
            })
            .catch((_) => {
              courseLayout.copyCourse.loading = false;

              $msg({
                text: $t(`gl.msg.error.sendData`),
                type: "error",
              });
            });

          // $msg({
          //   text: `${$t(`${lang}.course.msg.copy.sure.0`)} "${
          //     layoutDataStore.title
          //   }"${$t(`${lang}.course.msg.copy.sure.1`)}`,
          //   type: "sure",
          //   btns: {
          //     t() {

          //     },
          //     f() {
          //       courseLayout.copyCourse.loading = false;
          //     },
          //   },
          // });
        },
        clacDays() {
          const les = layoutDataStore.data.value.lessons; // les length
          let days = courseLayout.copyCourse.day;

          if (!courseLayout.copyCourse.isExtraDaysWeekend) {
            if (this.calcLearnDays(courseLayout.copyCourse.weekend) === 0) {
              courseLayout.copyCourse.weekend = [];
            } else {
              days = this.calcLearnDays(courseLayout.copyCourse.weekend);
              // console.log(calcLearnDays(weekend.value));
            }

            // console.log(dayStudyValue);
          }

          if (
            days > layoutDataStore.data.value.days_done_limit ||
            days < 1 ||
            isNaN(courseLayout.copyCourse.day)
          ) {
            courseLayout.copyCourse.result = {
              lessons: {
                value: 0,

                word: $t(`gl.lessons.0`),
              },
              days: {
                value: 0,
                word: $t(`gl.days.0`),
              },
            };

            if (courseLayout.copyCourse.day !== "") {
              $msg({
                text: `${$t(`${lang}.course.copy.done.days.error`)} ${
                  layoutDataStore.data.value.days_done_limit
                } ${$help().titleDay({
                  num: layoutDataStore.data.value.days_done_limit,
                })}.`,
                type: "error",
              });
            }

            return;
          }

          if (days <= les) {
            const minDay = Math.floor(les / days);
            const maxDay = Math.ceil(les / days);

            if (minDay !== maxDay) {
              courseLayout.copyCourse.result.lessons.value = `${minDay} ${$t(
                `gl.or`
              )} ${maxDay}`;
              courseLayout.copyCourse.result.lessons.word = $t(`gl.lessons.2`);
            } else {
              courseLayout.copyCourse.result.lessons.value = maxDay;
              courseLayout.copyCourse.result.lessons.word = $help().titleDay({
                num: minDay,
                word: $t(`gl.lessons`) as unknown as string[],
              });
            }
          } else {
            courseLayout.copyCourse.result.lessons.value = 1;
            courseLayout.copyCourse.result.lessons.word = `${$help().titleDay({
              num: 1,
              word: $t(`gl.lessons`) as unknown as string[],
            })}, ${$t(`${lang}.course.copy.done.result.text.3`)}`;
          }

          let dayLeft = courseLayout.copyCourse.day;

          if (courseLayout.copyCourse.isExtraDaysWeekend) {
            for (let i = 0; i < dayLeft; i++) {
              if (
                courseLayout.copyCourse.weekend.includes(
                  $help().getDate({ day: i, number: true })
                )
              ) {
                dayLeft++;
              }
            }
          }

          if (dayLeft - 1 === 0) {
            courseLayout.copyCourse.result.days.value = $t(
              `${lang}.course.copy.done.result.text.4`
            );
            courseLayout.copyCourse.result.days.word = "";
          } else {
            courseLayout.copyCourse.result.days.value = dayLeft - 1;
            courseLayout.copyCourse.result.days.word = $help().titleDay({
              num: dayLeft - 1,
            });
          }
        },
        extraDaysWeekendToggle(e: any) {
          courseLayout.copyCourse.isExtraDaysWeekend = e;
          this.clacDays();
        },
        addWeekend(index: number) {
          if (
            courseLayout.copyCourse.weekend.find((e) => e === index) !==
            undefined
          ) {
            courseLayout.copyCourse.weekend =
              courseLayout.copyCourse.weekend.filter((e) => e !== index);
          } else {
            if (courseLayout.copyCourse.weekend.length === 6) {
              $msg({
                text: $t(`${lang}.course.copy.done.weekend.error`),
                type: "error",
              });
              return;
            } else if (!courseLayout.copyCourse.isExtraDaysWeekend) {
              // console.log(calcLearnDays(weekend.value.concat([index])));
              if (
                this.calcLearnDays(
                  courseLayout.copyCourse.weekend.concat([index])
                ) === 0
              ) {
                // `تريد التعلم خلال ${courseLayout.copyCourse.day} أيام, وأيام الاجازة ${courseLayout.copyCourse.day} متى سوف تتعلم؟`
                $msg({
                  text: trans(`${lang}.course.copy.done.error`, {
                    day: courseLayout.copyCourse.day,
                  }),
                  type: "error",
                });
              }

              // 3 in wekened
            }

            courseLayout.copyCourse.weekend.push(index);
          }

          this.clacDays();
          // console.log(courseLayout.copyCourse.weekend);
        },
        calcLearnDays(weekend: number[]) {
          // console.log("run")
          // console.log("weekend",weekend);
          let LearnDay = courseLayout.copyCourse.day;
          for (let i = 0; i < courseLayout.copyCourse.day; i++) {
            const dayNow = $help().getDate({ day: i, number: true });
            // console.log("dayNow",dayNow,weekend.find((e) => e === dayNow))
            if (weekend.find((e) => e === dayNow) !== undefined) {
              LearnDay--;
              if (LearnDay == 0) {
                return 0;
              }
            }
          }

          // console.log("LearnDay",LearnDay)
          return LearnDay;
        },
      };
    },
  };
}

function sharing() {
  if (navigator.share) {
    navigator.share({
      title: layoutDataStore.title,
      url: `${$app().FR_URL.nashit}/${
        route.params.profile ? route.params.profile : $auth?.user?.username
      }/${path.value}/${route.params.id}/stats`,
    });
  } else if (navigator?.clipboard?.writeText) {
    navigator.clipboard.writeText(
      `${$app().FR_URL.nashit}/${
        route.params.profile ? route.params.profile : $auth?.user?.username
      }/${path.value}/${route.params.id}/stats`
    );

    $msg({
      text: $t(`gl.msg.share.linkCopied`),
      type: "ok",
    });
  } else {
    $msg({
      text: $t(`gl.msg.error.share`),
      type: "error",
    });
  }
}
</script>
<!-- <script lang="ts">
import selectElement from '~/components/elements/selectElement.vue';
import FullLoading from '~/components/full-loading.vue';

export default Vue.extend({
  components: {
    selectElement,
    FullLoading,
  },

  methods: {





  },
});
</script> -->
